# Código em Python (para Raspberry Pi 3 B + OpenCV)

Você quer começar um primeiro protótipo de código rodando no Raspberry Pi 3 Model B, com uma câmera conectada, para detectar movimento de veículos se aproximando.  
Como ainda estamos no início, vou te entregar um código mínimo viável (MVP de visão computacional) que:  
- Captura imagem da câmera (USB ou PiCam). 
- Detecta movimento com base em diferenças de quadros (frame differencing).
- (Opcional) Usa classificador pré-treinado (Haar Cascade) para tentar identificar carros (bem limitado, mas suficiente para prova de conceito).
- Registra no log (com timestamp) e pode futuramente enviar para backend (HTTP/MQTT).  
Assim você terá uma base que já roda no RPi3.

### O que esse protótipo faz
- Detecta movimento (diferença entre quadros).
- Usa classificador simples de carros (haarcascade_car.xml) para tentar identificar veículos na área em movimento.
- Exibe caixas verdes para movimento e azuis para detecção de veículo.
- Registra no terminal logs com timestamp quando detecta carro.

```python
import cv2
import datetime

# Inicializa câmera (0 = primeira câmera conectada USB ou PiCam habilitada)
cap = cv2.VideoCapture(0)

# Carrega classificador pré-treinado de carros (Haar Cascade do OpenCV)
car_cascade = cv2.CascadeClassifier(cv2.data.haarcascades + 'haarcascade_car.xml')

# Configura variáveis para comparação de movimento
ret, frame1 = cap.read()
ret, frame2 = cap.read()

print("[INFO] Iniciando monitoramento... Pressione 'q' para sair.")

while cap.isOpened():
    # Diferença entre quadros (detecção de movimento)
    diff = cv2.absdiff(frame1, frame2)
    gray = cv2.cvtColor(diff, cv2.COLOR_BGR2GRAY)
    blur = cv2.GaussianBlur(gray, (5, 5), 0)
    _, thresh = cv2.threshold(blur, 20, 255, cv2.THRESH_BINARY)
    dilated = cv2.dilate(thresh, None, iterations=3)
    contours, _ = cv2.findContours(dilated, cv2.RETR_TREE, cv2.CHAIN_APPROX_SIMPLE)

    # Desenha caixas ao redor de movimento detectado
    for contour in contours:
        if cv2.contourArea(contour) < 500:  # ignora ruídos pequenos
            continue
        (x, y, w, h) = cv2.boundingRect(contour)
        cv2.rectangle(frame1, (x, y), (x + w, y + h), (0, 255, 0), 2)

        # Aplica classificador de carros na região detectada
        roi_gray = gray[y:y+h, x:x+w]
        cars = car_cascade.detectMultiScale(roi_gray, 1.1, 1)
        for (cx, cy, cw, ch) in cars:
            cv2.rectangle(frame1, (x+cx, y+cy), (x+cx+cw, y+cy+ch), (255, 0, 0), 2)
            log_msg = f"Veículo detectado em {datetime.datetime.now()}"
            print(log_msg)

    # Exibe janela (pode remover no Raspberry para economizar recursos)
    cv2.imshow("Detecção de Veículos", frame1)

    frame1 = frame2
    ret, frame2 = cap.read()

    if not ret:
        break

    if cv2.waitKey(10) & 0xFF == ord('q'):
        break

cap.release()
cv2.destroyAllWindows()
```

### ⚠️ Limitação: HaarCascade é fraco para identificar modelos/placas. Para avançar, será necessário:
- Usar YOLOv5 nano ou MobileNet SSD (mais precisos e leves).
- Acrescentar OCR (pytesseract) para leitura de placas.
- Treinar modelo customizado para melhor recall em cruzamentos.

### Como rodar no Raspberry Pi 3

- Instale dependências:

``` bash
sudo apt update  
sudo apt install python3-opencv python3-pip  
pip3 install numpy  
```

- Conecte a câmera (USB ou habilite a PiCam com raspi-config).
- Salve o código como detector_carros.py. Rode:

```bash
python3 detector_carros.py  
```
