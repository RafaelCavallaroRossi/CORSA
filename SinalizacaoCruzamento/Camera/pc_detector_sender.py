import cv2
from ultralytics import YOLO
import socket
import time
import requests

# --- CONFIG ---
PI_IP = "192.168.0.199"  # Use o IP CORRETO do seu Pi
UDP_PORT = 5005
VIDEO_SOURCE = 0
CONF_THRESHOLD = 0.45
ID_PONTO_ATUAL = "P1"
WEB_API_URL = "http://localhost/PI4/SinalizacaoCruzamento/registrar_evento.php"

# ### MUDANÇA ### Tempo ajustado para 20 segundos
TEMPO_PARA_ESTACIONADO = 20  # segundos

VEICULOS_MAP = {
    2: "carro",
    3: "moto",
    5: "onibus",
    7: "caminhao"
}

# Comunicação UDP
sock = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)

# YOLO
modelo = YOLO("yolo11n.pt")

ultimo_estado_udp = None
tempo_inicio_deteccao = None
ultimo_estado_web = None

def registrar_evento_web(status_observacao, tipo_veiculo):
    global ultimo_estado_web
    estado_atual = f"{status_observacao}_{tipo_veiculo}"
    
    # Só envia se o status MUDOU
    if estado_atual != ultimo_estado_web:
        try:
            payload = {
                'id_ponto': ID_PONTO_ATUAL,
                'tipo': tipo_veiculo,
                'observacao': status_observacao  # 'Detectado' ou 'Estacionado'
            }
            response = requests.post(WEB_API_URL, data=payload, timeout=2.0)
            
            if response.status_code == 200:
                print(f"WEB: Evento '{estado_atual}' registrado com sucesso.")
            else:
                print(f"WEB: Erro {response.status_code} ao registrar evento: {response.text}")
            
            ultimo_estado_web = estado_atual
            
        except requests.exceptions.RequestException as e:
            print(f"WEB: Falha ao conectar com o servidor web: {e}")
            # Permite tentar de novo na próxima vez
            ultimo_estado_web = None

def enviar_sinal_udp(estado):
    global ultimo_estado_udp
    if estado != ultimo_estado_udp:
        msg = b"1" if estado else b"0"
        try:
            sock.sendto(msg, (PI_IP, UDP_PORT))
            ultimo_estado_udp = estado
            # ### MUDANÇA ### Nomenclatura do alerta
            print("UDP: SINAL ENVIADO:", "LED ACESO (VEÍCULO EM MOVIMENTO)" if estado else "LED APAGADO (LIVRE/ESTACIONADO)")
        except socket.gaierror as e:
            print(f"UDP ERRO: Falha ao encontrar o PI_IP '{PI_IP}'. Erro: {e}")
            ultimo_estado_udp = None
        except Exception as e:
            print(f"UDP ERRO: {e}")
            ultimo_estado_udp = None

cap = cv2.VideoCapture(VIDEO_SOURCE)
if not cap.isOpened():
    print(f"Erro: Não foi possível abrir a fonte de vídeo {VIDEO_SOURCE}")
    exit()

print("Iniciando detecção (Lógica de Estacionamento - 20s)...")

while True:
    ret, frame = cap.read()
    if not ret:
        print("Erro: Falha ao ler o frame da câmera.")
        time.sleep(1)
        continue

    resultados = modelo(frame, stream=True)
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
                cv2.rectangle(frame, (x1, y1), (x2, y2), (0, 255, 0), 2) # Verde
                break
        if veiculo_detectado:
            break

    # === LÓGICA DE ESTACIONAMENTO (CONFORME SOLICITADO) ===
    if veiculo_detectado:
        if tempo_inicio_deteccao is None:
            tempo_inicio_deteccao = time.time()

        tempo_decorrido = time.time() - tempo_inicio_deteccao

        # ### MUDANÇA ### Tempo alterado para 20 segundos
        if tempo_decorrido >= TEMPO_PARA_ESTACIONADO:
            # ESTACIONADO (Desconsiderar)
            print(f"🚗 Veículo '{tipo_veiculo_str}' → ESTACIONADO (Ignorando)")
            enviar_sinal_udp(False)  # Desliga LED
            registrar_evento_web("Estacionado", tipo_veiculo_str)
        else:
            # DETECTADO (Movendo)
            print(f"🚗 Veículo '{tipo_veiculo_str}' → DETECTADO (Em movimento)")
            enviar_sinal_udp(True)  # Acende LED
            registrar_evento_web("Detectado", tipo_veiculo_str)
            
    else:
        # RUA LIVRE
        if tempo_inicio_deteccao is not None:
             print("✅ Rua → LIVRE")
             
        tempo_inicio_deteccao = None
        enviar_sinal_udp(False)  # LED desliga
        
        # ### MUDANÇA PRINCIPAL (SEU PEDIDO) ###
        # Não enviamos "Livre" para o site.
        # Apenas resetamos o 'ultimo_estado_web' para que na próxima
        # detecção, ele envie "Detectado" imediatamente.
        ultimo_estado_web = None
        pass # Não faz nada (não envia "Livre")

    cv2.imshow("Detector de Veículos", frame)
    if cv2.waitKey(1) & 0xFF == ord('q'):
        break

sock.close()
cap.release()
cv2.destroyAllWindows()