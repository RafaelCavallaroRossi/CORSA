import socket
import RPi.GPIO as GPIO

UDP_PORT = 5005

LED_PIN = 18 
ID_PONTO_ESPERADO = "P1" 

GPIO.setmode(GPIO.BCM)
GPIO.setwarnings(False)
GPIO.setup(LED_PIN, GPIO.OUT)
GPIO.output(LED_PIN, GPIO.LOW)


sock = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
sock.bind(("", UDP_PORT))

print(f"Raspberry Pi aguardando dados na porta {UDP_PORT}...")
print(f"Controlando LED no pino {LED_PIN} para o ponto {ID_PONTO_ESPERADO}")

try:
    while True:
        data, addr = sock.recvfrom(1024)
        mensagem = data.decode('utf-8').strip()
        

        if ":" in mensagem:
            ponto_recebido, estado = mensagem.split(":")
            
 
            if ponto_recebido == ID_PONTO_ESPERADO:
                if estado == "1":
                    GPIO.output(LED_PIN, GPIO.HIGH)
                    print(f"🚗 {ponto_recebido}: DETECTADO -> LED ACESO")
                else:
                    GPIO.output(LED_PIN, GPIO.LOW)
                    print(f"✅ {ponto_recebido}: LIVRE -> LED APAGADO")
        else:

            if mensagem == "1":
                 GPIO.output(LED_PIN, GPIO.HIGH)
            elif mensagem == "0":
                 GPIO.output(LED_PIN, GPIO.LOW)

except KeyboardInterrupt:
    print("Encerrando...")
    GPIO.cleanup()
    sock.close()