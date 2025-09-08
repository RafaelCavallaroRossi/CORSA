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

### ⚠️ Limitação: HaarCascade é fraco para identificar modelos/placas. Para avançar, será necessário:
- Usar YOLOv5 nano ou MobileNet SSD (mais precisos e leves).
- Acrescentar OCR (pytesseract) para leitura de placas.
- Treinar modelo customizado para melhor recall em cruzamentos.

### Como rodar no Raspberry Pi 3

- Instale dependências:

> sudo apt update  
sudo apt install python3-opencv python3-pip  
pip3 install numpy  


- Conecte a câmera (USB ou habilite a PiCam com raspi-config).
- Salve o código como detector_carros.py. Rode:

> python3 detector_carros.py  
