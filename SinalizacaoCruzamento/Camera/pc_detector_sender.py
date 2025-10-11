# pc_detector_sender.py
import time
import threading
import requests
import cv2
import numpy as np
from collections import deque
from ultralytics import YOLO
from datetime import datetime

# ---------- CONFIGURAÇÃO ----------
VIDEO_SOURCE = 0              # 0 = webcam. Pode ser "carros.mp4" ou "rtsp://..."
YAOLO_MODEL = "yolo11n.pt"    # caminho para o modelo (já baixado)
LIMIT_CONFIANCA = 0.45
VEICULOS_CLASSES = {2:"Carro", 3:"Moto", 5:"Onibus", 7:"Caminhao"}  # COCO ids relevantes

PI_URL = "http://10.186.150.9:5000/update"  # <-- Troque pelo IP do seu Raspberry Pi
SEND_INTERVAL = 1.0            # segundos entre POSTs ao Pi

# Parâmetros de movimento / estacionamento
MOVE_TOLERANCE = 15            # pixels — movimento mínimo para considerar "em movimento"
PARK_SECONDS = 10.0            # se sem movimento por X secs, considera estacionado

# ---------- RASTREADOR SIMPLES ----------
class SimpleTracker:
    def __init__(self, max_disappeared=50):
        self.next_id = 0
        self.objects = {}  # id -> centroid (x,y)
        self.disappeared = {}  # id -> frames desaparecido
        # para lógica de estacionamento:
        self.last_moved_time = {}  # id -> timestamp of last movement
        self.last_position = {}    # id -> last centroid
        self.max_disappeared = max_disappeared

    def register(self, centroid):
        i = self.next_id
        self.objects[i] = centroid
        self.disappeared[i] = 0
        self.last_position[i] = centroid
        self.last_moved_time[i] = time.time()
        self.next_id += 1

    def deregister(self, oid):
        for d in (self.objects, self.disappeared, self.last_moved_time, self.last_position):
            if oid in d:
                del d[oid]

    def update(self, input_centroids):
        # input_centroids: list of (x,y)
        if len(input_centroids) == 0:
            # increment disappeared count
            for oid in list(self.disappeared.keys()):
                self.disappeared[oid] += 1
                if self.disappeared[oid] > self.max_disappeared:
                    self.deregister(oid)
            return self.objects

        if len(self.objects) == 0:
            for c in input_centroids:
                self.register(c)
            return self.objects

        # build arrays
        object_ids = list(self.objects.keys())
        object_centroids = list(self.objects.values())

        D = np.linalg.norm(np.array(object_centroids)[:, np.newaxis] - np.array(input_centroids), axis=2)
        rows = D.min(axis=1).argsort()
        cols = D.argmin(axis=1)[rows]

        used_rows, used_cols = set(), set()
        for row, col in zip(rows, cols):
            if row in used_rows or col in used_cols:
                continue
            oid = object_ids[row]
            new_centroid = input_centroids[col]
            # update disappeared
            self.disappeared[oid] = 0
            # check movement distance
            old = self.last_position.get(oid, new_centroid)
            dist = np.linalg.norm(np.array(old) - np.array(new_centroid))
            if dist >= MOVE_TOLERANCE:
                self.last_moved_time[oid] = time.time()
            self.last_position[oid] = new_centroid
            self.objects[oid] = new_centroid
            used_rows.add(row)
            used_cols.add(col)

        # rows not matched -> disappeared
        unused_rows = set(range(0, D.shape[0])) - used_rows
        for row in unused_rows:
            oid = object_ids[row]
            self.disappeared[oid] += 1
            if self.disappeared[oid] > self.max_disappeared:
                self.deregister(oid)

        # cols not matched -> new objects
        unused_cols = set(range(0, D.shape[1])) - used_cols
        for col in unused_cols:
            self.register(input_centroids[col])

        return self.objects

    def get_active_ids(self):
        """Retorna IDs que NÃO estão estacionados (moved within PARK_SECONDS)"""
        now = time.time()
        active = []
        for oid, centroid in self.objects.items():
            last_moved = self.last_moved_time.get(oid, 0)
            if (now - last_moved) <= PARK_SECONDS:
                active.append(oid)
        return active

# ---------- ENVIADOR ASSÍNCRONO ----------
class SenderThread(threading.Thread):
    def __init__(self, url, interval=1.0):
        super().__init__(daemon=True)
        self.url = url
        self.interval = interval
        self.payload = {"vehicle_present": False, "moving_count": 0}
        self._stop = threading.Event()

    def run(self):
        while not self._stop.is_set():
            try:
                requests.post(self.url, json=self.payload, timeout=1.5)
            except Exception as e:
                # opcional: print("Erro ao enviar para Pi:", e)
                pass
            time.sleep(self.interval)

    def update_payload(self, present, moving_count):
        self.payload = {"vehicle_present": bool(present), "moving_count": int(moving_count)}

    def stop(self):
        self._stop.set()

# ---------- LÓGICA PRINCIPAL ----------
def main():
    model = YOLO(YAOLO_MODEL)
    cap = cv2.VideoCapture(VIDEO_SOURCE)
    if not cap.isOpened():
        print("Erro ao abrir fonte:", VIDEO_SOURCE)
        return

    tracker = SimpleTracker()
    sender = SenderThread(PI_URL, interval=SEND_INTERVAL)
    sender.start()

    try:
        while True:
            ret, frame = cap.read()
            if not ret:
                break

            # run detection (note: may be heavy; adjust skip frames if needed)
            results = model(frame)

            # collect centroids for vehicle classes
            centroids = []
            for r in results:
                for box in r.boxes:
                    cls_id = int(box.cls[0])
                    conf = float(box.conf[0])
                    if conf < LIMIT_CONFIANCA:
                        continue
                    if cls_id not in VEICULOS_CLASSES:
                        continue
                    x1, y1, x2, y2 = map(int, box.xyxy[0])
                    # optional: skip tiny boxes
                    area = (x2 - x1) * (y2 - y1)
                    if area < 500:  # ajuste conforme vídeo
                        continue
                    cx, cy = (x1 + x2) // 2, (y1 + y2) // 2
                    centroids.append((cx, cy))
                    # draw box
                    name = VEICULOS_CLASSES[cls_id]
                    cv2.rectangle(frame, (x1, y1), (x2, y2), (0,255,0), 2)
                    cv2.putText(frame, f"{name} {conf:.2f}", (x1, y1-8), cv2.FONT_HERSHEY_SIMPLEX, 0.5, (0,255,0), 1)

            # update tracker
            objs = tracker.update(centroids)
            active_ids = tracker.get_active_ids()  # ids considered "moving" (not parked)

            # show tracker ids on frame
            for oid, centroid in objs.items():
                cx, cy = map(int, centroid)
                text = f"ID{oid}"
                if oid in active_ids:
                    color = (0,255,0)
                else:
                    color = (0,0,255)  # parked = red id
                cv2.putText(frame, text, (cx-10, cy-10), cv2.FONT_HERSHEY_SIMPLEX, 0.5, color, 1)
                cv2.circle(frame, (cx,cy), 4, color, -1)

            # update sender payload
            sender.update_payload(present=len(active_ids)>0, moving_count=len(active_ids))

            cv2.imshow("PC - Detector", frame)
            if cv2.waitKey(1) & 0xFF == ord('q'):
                break
    finally:
        sender.stop()
        sender.join(timeout=2.0)
        cap.release()
        cv2.destroyAllWindows()

if __name__ == "__main__":
    main()
