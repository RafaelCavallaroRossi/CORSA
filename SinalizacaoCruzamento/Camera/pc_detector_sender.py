import cv2
from ultralytics import YOLO
import socket
import time
import requests
import threading
import queue
from flask import Flask, Response

PI_IP = "10.72.99.9" #ip do raspbarry PI
UDP_PORT = 5005
CONF_THRESHOLD = 0.45
TEMPO_PARA_ESTACIONADO = 20

CAMERAS = {
    0: "P1" # Camera ou video.
}
WEB_API_URL = "http://localhost/PI4/SinalizacaoCruzamento/registrar_evento.php"
FLASK_PORT = 5001

VEICULOS_MAP = { 2: "carro", 3: "moto", 5: "onibus", 7: "caminhao" }

sock = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
modelo = YOLO("yolo11n.pt")

tempo_inicio_deteccao = {id_ponto: None for id_ponto in CAMERAS.values()}
ultimo_estado_web = {id_ponto: None for id_ponto in CAMERAS.values()}
moving_status = {id_ponto: False for id_ponto in CAMERAS.values()}
ultimo_estado_udp = {id_ponto: None for id_ponto in CAMERAS.values()}

frame_queue = queue.Queue(maxsize=len(CAMERAS))

app = Flask(__name__)
stream_frames_jpeg = {id_ponto: None for id_ponto in CAMERAS.values()}
frame_lock = threading.Lock()

def registrar_evento_web(id_ponto, status_observacao, tipo_veiculo):
    global ultimo_estado_web
    estado_atual = f"{status_observacao}_{tipo_veiculo}"
    if estado_atual != ultimo_estado_web[id_ponto]:
        try:
            payload = { 'id_ponto': id_ponto, 'tipo': tipo_veiculo, 'observacao': status_observacao }
            requests.post(WEB_API_URL, data=payload, timeout=2.0)
            print(f"WEB [{id_ponto}]: Evento '{estado_atual}' registrado.")
            ultimo_estado_web[id_ponto] = estado_atual
        except Exception as e:
            print(f"WEB [{id_ponto}]: Falha ao conectar: {e}")
            ultimo_estado_web[id_ponto] = None

def capture_thread(camera_index, id_ponto):
    print(f"Iniciando thread de captura para {id_ponto} (Fonte: {camera_index})...")
    cap = cv2.VideoCapture(camera_index)
    
    is_video_file = isinstance(camera_index, str)
    
    if not cap.isOpened():
        print(f"ERRO: Não foi possível abrir a fonte {camera_index} para {id_ponto}")
        return
        
    while True:
        ret, frame = cap.read()
        
        if not ret:
            if is_video_file:
                cap.set(cv2.CAP_PROP_POS_FRAMES, 0)
                continue
            else:
                print(f"Erro ao ler Câmera {id_ponto}. Tentando reconectar...")
                cap.release(); time.sleep(2); cap = cv2.VideoCapture(camera_index)
                if not cap.isOpened():
                    print(f"Falha ao reconectar {id_ponto}. Encerrando thread.")
                    break
                continue
        
        try:
            frame_queue.put((frame, id_ponto), timeout=1) 
        except queue.Full:
            pass 
        
        time.sleep(0.01)

def generate_video_feed(id_ponto):
    while True:
        time.sleep(0.05)
        with frame_lock:
            frame_bytes = stream_frames_jpeg.get(id_ponto)

        if frame_bytes is None:
            placeholder_path = "placeholder.jpg"
            placeholder_img = cv2.imread(placeholder_path)
            if placeholder_img is not None:
                _, buffer = cv2.imencode('.jpg', placeholder_img)
                frame_bytes = buffer.tobytes()
            else:
                frame_bytes = b'' 

        if frame_bytes:
            yield (b'--frame\r\n'
                   b'Content-Type: image/jpeg\r\n\r\n' + frame_bytes + b'\r\n')

@app.route('/video_feed/<id_ponto>')
def video_feed(id_ponto):
    if id_ponto not in CAMERAS.values():
        return "Ponto de câmera inválido", 404
    
    return Response(generate_video_feed(id_ponto),
                    mimetype='multipart/x-mixed-replace; boundary=frame')

def run_flask_server():
    print(f"Iniciando servidor de stream em http://0.0.0.0:{FLASK_PORT}")
    app.run(host='0.0.0.0', port=FLASK_PORT, threaded=True, debug=False)

for cam_index, id_ponto in CAMERAS.items():
    t = threading.Thread(target=capture_thread, args=(cam_index, id_ponto), daemon=True)
    t.start()

flask_thread = threading.Thread(target=run_flask_server, daemon=True)
flask_thread.start()

print("Iniciando processamento (YOLO)... Aguardando frames...")

while True:
    try:
        frame, id_ponto_frame = frame_queue.get(timeout=5)
    except queue.Empty:
        print("Fila de frames (YOLO) vazia por 5s.")
        continue

    resultados = modelo(frame, stream=True, verbose=False)
    veiculo_detectado = False
    tipo_veiculo_str = "indefinido"

    for r in resultados:
        for box in r.boxes:
            cls = int(box.cls[0])
            conf = float(box.conf[0])
            if conf >= CONF_THRESHOLD and cls in VEICULOS_MAP:
                veiculo_detectado = True
                tipo_veiculo_str = VEICULOS_MAP[cls]
                
                x1, y1, x2, y2 = map(int, box.xyxy[0])
                cv2.rectangle(frame, (x1, y1), (x2, y2), (0, 255, 0), 2)
                cv2.putText(frame, tipo_veiculo_str, (x1, y1 - 10), cv2.FONT_HERSHEY_SIMPLEX, 0.5, (0, 255, 0), 2)
                break
        if veiculo_detectado: break

    if veiculo_detectado:
        if tempo_inicio_deteccao[id_ponto_frame] is None:
            tempo_inicio_deteccao[id_ponto_frame] = time.time()
        tempo_decorrido = time.time() - tempo_inicio_deteccao[id_ponto_frame]
        
        if tempo_decorrido >= TEMPO_PARA_ESTACIONADO:
            moving_status[id_ponto_frame] = False
            registrar_evento_web(id_ponto_frame, "Estacionado", tipo_veiculo_str)
        else:
            moving_status[id_ponto_frame] = True
            registrar_evento_web(id_ponto_frame, "Detectado", tipo_veiculo_str)
    else:
        tempo_inicio_deteccao[id_ponto_frame] = None
        moving_status[id_ponto_frame] = False
        ultimo_estado_web[id_ponto_frame] = None
        
    estado_movimento_atual = moving_status[id_ponto_frame]
    novo_estado_udp = "1" if estado_movimento_atual else "0"
    if novo_estado_udp != ultimo_estado_udp[id_ponto_frame]:
        msg_str = f"{id_ponto_frame}:{novo_estado_udp}"
        try:
            sock.sendto(msg_str.encode('utf-8'), (PI_IP, UDP_PORT))
            print(f"UDP [{id_ponto_frame}]: Sinal '{msg_str}' enviado.")
            ultimo_estado_udp[id_ponto_frame] = novo_estado_udp
        except Exception as e:
            print(f"UDP [{id_ponto_frame}] ERRO: {e}")
            
    status_texto = "MOVIMENTO" if moving_status[id_ponto_frame] else "LIVRE/PARADO"
    cor_texto = (0, 0, 255) if moving_status[id_ponto_frame] else (0, 255, 0)
    cv2.putText(frame, f"PONTO: {id_ponto_frame} | STATUS: {status_texto}", (10, 30), cv2.FONT_HERSHEY_SIMPLEX, 0.7, cor_texto, 2)
    
    ret, buffer = cv2.imencode('.jpg', frame, [int(cv2.IMWRITE_JPEG_QUALITY), 80])
    if ret:
        with frame_lock:
            stream_frames_jpeg[id_ponto_frame] = buffer.tobytes()

print("Encerrando... (Se o servidor Flask estiver rodando, use Ctrl+C)")
sock.close()
