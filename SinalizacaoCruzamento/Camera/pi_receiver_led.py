import socket
import RPi.GPIO as GPIO

LED_PIN = 18

GPIO.setmode(GPIO.BCM)
GPIO.setup(LED_PIN, GPIO.OUT)
GPIO.output(LED_PIN, GPIO.LOW)

sock = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
sock.bind(("", 5005))

print("Aguardando sinais do PC...")

while True:
    data, addr = sock.recvfrom(1024)

    if data == b"1":
        GPIO.output(LED_PIN, GPIO.HIGH)
        print("🚗 VEÍCULO DETECTADO → LED ACESO")
    else:
        GPIO.output(LED_PIN, GPIO.LOW)
        print("✅ LIVRE / ESTACIONADO → LED APAGADO")
