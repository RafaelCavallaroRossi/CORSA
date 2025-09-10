"""
CORSA - PoC Melhorado
Pipeline modular para detecção de veículos (Edge)
Pronto para evoluir para modelos TFLite/YOLO e integração com backend/telemetria.
"""

import cv2
import datetime
import logging
import os

# Configuração de logging estruturado
logging.basicConfig(
    level=logging.INFO,
    format="%(asctime)s [%(levelname)s] %(message)s",
    handlers=[
        logging.FileHandler("corsa.log"),
        logging.StreamHandler()
    ]
)

# Parâmetros configuráveis
MIN_CONTOUR_AREA = 500
CAMERA_INDEX = 0

def inicializar_camera(index=CAMERA_INDEX):
    """
    Inicializa e retorna o objeto de captura da câmera.
    Lança exceção se não conseguir acessar a câmera.
    """
    cap = cv2.VideoCapture(index)
    if not cap.isOpened():
        logging.error("Não foi possível acessar a câmera.")
        raise RuntimeError("Falha ao abrir a câmera.")
    return cap

def carregar_modelo_haar():
    """
    Carrega o classificador Haar Cascade para detecção de veículos.
    Lança exceção se o arquivo não for encontrado.
    """
    cascade_path = cv2.data.haarcascades + 'haarcascade_car.xml'
    if not os.path.exists(cascade_path):
        logging.error("Arquivo haarcascade_car.xml não encontrado.")
        raise FileNotFoundError("Cascade não encontrado.")
    return cv2.CascadeClassifier(cascade_path)

def detectar_veiculos(frame, car_cascade):
    """
    Recebe um frame e o classificador Haar Cascade.
    Retorna as regiões detectadas como possíveis veículos.
    """
    gray = cv2.cvtColor(frame, cv2.COLOR_BGR2GRAY)
    cars = car_cascade.detectMultiScale(gray, 1.1, 1)
    return cars

def pipeline_captura_e_inferencia():
    """
    Pipeline principal:
    - Inicializa câmera e modelo
    - Realiza detecção de movimento e veículos em tempo real
    - Exibe resultados e registra logs
    - Encerra ao pressionar 'q' ou erro de captura
    """
    cap = inicializar_camera()
    car_cascade = carregar_modelo_haar()
    total_carros = 0

    ret, frame1 = cap.read()
    ret, frame2 = cap.read()

    logging.info("CORSA iniciado - Pressione 'q' para sair.")

    while cap.isOpened():
        diff = cv2.absdiff(frame1, frame2)
        gray = cv2.cvtColor(diff, cv2.COLOR_BGR2GRAY)
        blur = cv2.GaussianBlur(gray, (5, 5), 0)
        _, thresh = cv2.threshold(blur, 20, 255, cv2.THRESH_BINARY)
        dilated = cv2.dilate(thresh, None, iterations=3)
        contours, _ = cv2.findContours(dilated, cv2.RETR_TREE, cv2.CHAIN_APPROX_SIMPLE)

        for contour in contours:
            if cv2.contourArea(contour) < MIN_CONTOUR_AREA:
                continue
            (x, y, w, h) = cv2.boundingRect(contour)
            cv2.rectangle(frame1, (x, y), (x + w, y + h), (0, 255, 0), 2)

            roi = frame1[y:y+h, x:x+w]
            cars = detectar_veiculos(roi, car_cascade)

            for (cx, cy, cw, ch) in cars:
                cv2.rectangle(frame1, (x+cx, y+cy), (x+cx+cw, y+cy+ch), (255, 0, 0), 2)
                total_carros += 1
                logging.info(f"Veículo detectado. Total: {total_carros}")

        cv2.putText(frame1, f"Total veiculos: {total_carros}", (20, 40),
                    cv2.FONT_HERSHEY_SIMPLEX, 1, (0, 0, 255), 2)
        cv2.imshow("CORSA - PoC", frame1)

        frame1 = frame2
        ret, frame2 = cap.read()
        if not ret:
            logging.warning("Frame não capturado. Encerrando.")
            break

        if cv2.waitKey(10) & 0xFF == ord('q'):
            break

    cap.release()
    cv2.destroyAllWindows()
    logging.info("CORSA finalizado.")

if __name__ == "__main__":
    try:
        pipeline_captura_e_inferencia()
    except Exception as e:
        logging.exception(f"Erro fatal: {e}")
