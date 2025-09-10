# Importa bibliotecas necessárias
import cv2                     # OpenCV para processamento de vídeo/imagens
import numpy as np             # NumPy para cálculos numéricos
from ultralytics import YOLO   # Modelo YOLOv11 para detecção de objetos
from pymongo import MongoClient  # Conexão com banco de dados MongoDB
from datetime import datetime, timedelta  # Trabalhar com data e hora

# Captura a data/hora atuais
hoje = datetime.now()
hora_formatada_completa = hoje.strftime("%H:%M:%S")
dataFormatada = hoje.strftime("%d-%m-%Y")

# --- Funções de conexão e salvamento no MongoDB ---
def conectar_mongodb():
    """Conecta ao MongoDB e retorna a coleção onde os dados serão salvos"""
    client = MongoClient("mongodb://localhost:27017/")
    db = client['contagem_veiculos']
    return db['dados_veiculos']

def salvar_dados_mongodb(colecao, dados):
    """Insere os dados de contagem de veículos na coleção"""
    colecao.insert_one(dados)


# --- Classe para rastrear veículos pelo centroide (ponto central da caixa detectada) ---
class RastreadorDeCentroides:
    def __init__(self, max_desaparecidos=50):
        # ID para o próximo objeto detectado
        self.proximo_id_objeto = 0
        # Dicionário de objetos ativos (id → centroide)
        self.objetos = {}
        # Dicionário de contagem de "desaparecimentos" de cada objeto
        self.desaparecidos = {}
        self.max_desaparecidos = max_desaparecidos

    def registrar(self, centroide):
        """Adiciona novo objeto rastreado"""
        self.objetos[self.proximo_id_objeto] = centroide
        self.desaparecidos[self.proximo_id_objeto] = 0
        self.proximo_id_objeto += 1

    def remover(self, id_objeto):
        """Remove objeto que sumiu há muito tempo"""
        del self.objetos[id_objeto]
        del self.desaparecidos[id_objeto]

    def atualizar(self, centroides_entrada):
        """
        Atualiza o estado do rastreador com os novos centroides detectados
        """
        # Caso nenhum centroide seja detectado neste frame
        if len(centroides_entrada) == 0:
            for id_objeto in list(self.desaparecidos.keys()):
                self.desaparecidos[id_objeto] += 1
                # Remove se ficou ausente além do limite
                if self.desaparecidos[id_objeto] > self.max_desaparecidos:
                    self.remover(id_objeto)
            return self.objetos

        # Se ainda não há objetos rastreados, registra todos
        if len(self.objetos) == 0:
            for i in range(0, len(centroides_entrada)):
                self.registrar(centroides_entrada[i])
        else:
            # Lista de IDs e centroides atuais
            ids_objetos = list(self.objetos.keys())
            centroides_objetos = list(self.objetos.values())

            # Calcula distâncias entre centroides atuais e novos
            distancias = self._distancias_euclidianas(centroides_objetos, centroides_entrada)

            # Associa objetos aos novos centroides pela menor distância
            linhas = distancias.min(axis=1).argsort()
            colunas = distancias.argmin(axis=1)[linhas]

            linhas_usadas = set()
            colunas_usadas = set()

            for (linha, coluna) in zip(linhas, colunas):
                if linha in linhas_usadas or coluna in colunas_usadas:
                    continue

                id_objeto = ids_objetos[linha]
                # Atualiza posição do objeto
                self.objetos[id_objeto] = centroides_entrada[coluna]
                self.desaparecidos[id_objeto] = 0

                linhas_usadas.add(linha)
                colunas_usadas.add(coluna)

            # Identifica objetos que não foram atualizados
            linhas_nao_usadas = set(range(0, distancias.shape[0])).difference(linhas_usadas)
            colunas_nao_usadas = set(range(0, distancias.shape[1])).difference(colunas_usadas)

            # Se havia mais objetos do que novos centroides
            if distancias.shape[0] >= distancias.shape[1]:
                for linha in linhas_nao_usadas:
                    id_objeto = ids_objetos[linha]
                    self.desaparecidos[id_objeto] += 1
                    if self.desaparecidos[id_objeto] > self.max_desaparecidos:
                        self.remover(id_objeto)
            else:
                # Se surgiram novos centroides, registrar como novos objetos
                for coluna in colunas_nao_usadas:
                    self.registrar(centroides_entrada[coluna])

        return self.objetos

    def _distancias_euclidianas(self, ptsA, ptsB):
        """Calcula matriz de distâncias euclidianas entre pontos A e B"""
        distancias = np.linalg.norm(np.array(ptsA)[:, np.newaxis] - np.array(ptsB), axis=2)
        return distancias


# --- Função principal para processar o vídeo ---
def processar_video_yolov11(fonte_video):
    modelo = YOLO('yolo11n.pt')  # Carrega modelo YOLO pré-treinado
    captura = cv2.VideoCapture(fonte_video)  # Abre stream do vídeo

    if not captura.isOpened():
        print(f"Erro: Não foi possível abrir a fonte de vídeo: {fonte_video}")
        return

    # Contadores
    total_carros = 0
    total_motos = 0

    # Rastreadores separados para carros e motos
    rastreador_carros = RastreadorDeCentroides(max_desaparecidos=50)
    rastreador_motos = RastreadorDeCentroides(max_desaparecidos=50)

    # IDs já contados (evita contar o mesmo veículo várias vezes)
    ids_contados_carros = set()
    ids_contados_motos = set()

    limite_confianca = 0.4  # Threshold de confiança YOLO
    colecao_veiculos = conectar_mongodb()

    # Define intervalo de contagem por hora cheia
    agora = datetime.now()
    minutos_para_proxima_hora = (60 - agora.minute) % 60
    inicio_contagem = agora + timedelta(minutes=minutos_para_proxima_hora)
    inicio_contagem = inicio_contagem.replace(minute=0, second=0, microsecond=0)
    fim_contagem = inicio_contagem + timedelta(hours=1)

    while captura.isOpened():
        ret, quadro = captura.read()
        if not ret:
            print("Fim do stream ou erro de leitura.")
            break

        # Executa detecção YOLO
        resultados = modelo(quadro)

        # Listas de centroides detectados
        centroides_entrada_carros = []
        centroides_entrada_motos = []

        # Itera sobre as detecções
        for resultado in resultados:
            for caixa in resultado.boxes:
                id_classe = int(caixa.cls[0])  # Classe detectada
                confianca = caixa.conf[0]       # Confiança da detecção

                if confianca >= limite_confianca:
                    # Coordenadas da caixa delimitadora
                    x1, y1, x2, y2 = map(int, caixa.xyxy[0])
                    # Calcula centroide
                    centroide_x = (x1 + x2) // 2
                    centroide_y = (y1 + y2) // 2
                     
                    # Classe 2 = carro, Classe 3 = moto (COCO dataset)
                    if id_classe == 2:
                        centroides_entrada_carros.append((centroide_x, centroide_y))
                        # Desenha retângulo verde
                        cv2.rectangle(quadro, (x1, y1), (x2, y2), (0, 255, 0), 2)
                        cv2.putText(quadro, f"Carro {confianca:.2f}", (x1, y1 - 10),
                                    cv2.FONT_HERSHEY_SIMPLEX, 0.6, (0, 255, 0), 2)

                    elif id_classe == 3:
                        centroides_entrada_motos.append((centroide_x, centroide_y))
                        # Desenha retângulo azul
                        cv2.rectangle(quadro, (x1, y1), (x2, y2), (255, 0, 0), 2)
                        cv2.putText(quadro, f"Moto {confianca:.2f}", (x1, y1 - 10),
                                    cv2.FONT_HERSHEY_SIMPLEX, 0.6, (255, 0, 0), 2)

        # Atualiza rastreadores com os novos centroides
        objetos_carros = rastreador_carros.atualizar(centroides_entrada_carros)
        objetos_motos = rastreador_motos.atualizar(centroides_entrada_motos)

        # Incrementa contadores se encontrar novos IDs
        for (id_objeto, centroide) in objetos_carros.items():
            if id_objeto not in ids_contados_carros:
                total_carros += 1
                ids_contados_carros.add(id_objeto)

        for (id_objeto, centroide) in objetos_motos.items():
            if id_objeto not in ids_contados_motos:
                total_motos += 1
                ids_contados_motos.add(id_objeto)

        total_veiculos = total_carros + total_motos

        # Verifica se completou 1 hora de contagem
        agora = datetime.now()
        if agora >= fim_contagem:
            intervalo_tempo = f"{inicio_contagem.strftime('%Hh')} - {fim_contagem.strftime('%Hh')}"

            # Dados a serem salvos
            dados_veiculos = {
                "data": dataFormatada,
                "hora": hora_formatada_completa,
                "rua": "Teste",
                "latitude": "-22.4362174",
                "longitude": "-46.8228786",
                "carros": total_carros,
                "motos": total_motos,
                "caminhoes": 0,
                "onibus": 0,
                "vans": 0,
                "total_veiculos": total_veiculos,
            }

            salvar_dados_mongodb(colecao_veiculos, dados_veiculos)
            print(f"Dados salvos no MongoDB para o intervalo: {intervalo_tempo}")

            # Reinicia contagem para a próxima hora
            total_carros = 0
            total_motos = 0
            ids_contados_carros.clear()
            ids_contados_motos.clear()

            inicio_contagem = fim_contagem
            fim_contagem = inicio_contagem + timedelta(hours=1)

        # Escreve textos no vídeo
        cv2.putText(quadro, f"Carros detectados: {total_carros}", (20, 40),
                    cv2.FONT_HERSHEY_SIMPLEX, 1, (0, 0, 255), 2)
        cv2.putText(quadro, f"Motos detectadas: {total_motos}", (20, 80),
                    cv2.FONT_HERSHEY_SIMPLEX, 1, (255, 0, 0), 2)
        cv2.putText(quadro, f"Total de veiculos: {total_veiculos}", (20, 120),
                    cv2.FONT_HERSHEY_SIMPLEX, 1, (0, 255, 255), 2)

        # Mostra imagem com as detecções
        cv2.imshow("Detecção e Contagem de Veículos - YOLOv11", quadro)

        # Sai do loop se apertar a tecla 'q'
        if cv2.waitKey(1) & 0xFF == ord('q'):
            break

    # Mostra resultado final da última hora
    print(f"Total de carros contados na última hora: {total_carros}")
    print(f"Total de motos contadas na última hora: {total_motos}")
    print(f"Total de veiculos contados na última hora: {total_veiculos}")

    captura.release()
    cv2.destroyAllWindows()


# --- Execução principal ---
rtsp_url = "rtsp://admin:123456@192.168.1.86:554/H264?ch=1&subtype=0"  # URL da câmera IP (exemplo)
processar_video_yolov11(rtsp_url)
