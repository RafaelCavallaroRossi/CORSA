# pi_receiver_led.py
from flask import Flask, request, jsonify
import RPi.GPIO as GPIO
import threading
import time

# ---------- CONFIG ----------
LED_PIN = 18   # GPIO BCM pin onde o LED está conectado
HOST = "0.0.0.0"
PORT = 5000

# ---------- SETUP GPIO ----------
GPIO.setmode(GPIO.BCM)
GPIO.setup(LED_PIN, GPIO.OUT)
GPIO.output(LED_PIN, GPIO.LOW)

app = Flask(__name__)

# Estado atual (thread-safe)
state = {"vehicle_present": False, "moving_count": 0}
lock = threading.Lock()

@app.route("/update", methods=["POST"])
def update():
    data = request.get_json(silent=True)
    if not data:
        return jsonify({"error": "invalid json"}), 400

    vehicle_present = bool(data.get("vehicle_present", False))
    moving_count = int(data.get("moving_count", 0))

    with lock:
        state["vehicle_present"] = vehicle_present
        state["moving_count"] = moving_count
        # controla o LED imediatamente
        GPIO.output(LED_PIN, GPIO.HIGH if vehicle_present else GPIO.LOW)

    return jsonify({"status":"ok"}), 200

@app.route("/status", methods=["GET"])
def status():
    with lock:
        return jsonify(state)

def run_flask():
    # flask built-in server (suficiente para LAN + baixo throughput)
    app.run(host=HOST, port=PORT, threaded=True)

if __name__ == "__main__":
    try:
        print("Iniciando receptor no Raspberry Pi...")
        run_flask()
    except KeyboardInterrupt:
        pass
    finally:
        GPIO.output(LED_PIN, GPIO.LOW)
        GPIO.cleanup()
