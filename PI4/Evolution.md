## Fase 1 — PoC (Edge-only, RPi básico)

- Objetivo: provar o conceito com hardware barato e código simples que detecta aproximação/veículos e gera logs.
Base: detector_carros.py (CodigosIniciais.md).

O que fazer (técnico / código)  
Manter a lógica de frame differencing para detectar movimento.  
Usar HaarCascade para tentativa inicial de identificação de veículos.  

- Encapsular código num módulo com funções (ex.: capture.py, detector.py) para facilitar evolução:

capture.py — inicializa câmera e entrega frames;  
detector.py — detecta movimento / aplica cascade;  
main_poc.py — orquestra, escreve logs locais (events.log) e aciona GPIO (simulado).  
Adicionar configuração em config.json (sensitivity, min_contour_area, camera_index, log_path).  
Adicionar nível mínimo de resilência: watchdog que reinicia script em crash (systemd service).  

- Entregáveis

Código PoC em repositório (edge/poc/).  
README com instruções de instalação e execução no RPi.  
events.log com timestamp e contagens.  

- Critérios de aceitação

Roda no RPi sem travar (auto-restart via systemd).  
Reporta eventos no log quando há movimento/veículo detectado.  

## Fase 2 — Lab Validation (Edge optimized)

- Objetivo: melhorar recall/latência no edge, reduzir falsos positivos e medir métricas básicas.

O que fazer (técnico / código)  
Substituir HaarCascade por um modelo leve: MobileNet-SSD ou YOLO “nano” / Tiny (ou quantizado TFLite) para rodar no RPi.  
Pipeline: captura → preproc (resize, normalize) → inferência TFLite/PyTorch Mobile → post-filter (zona).  
Implementar post-processing simples:  
Non-max suppression, filtragem por caixa mínima, confirmação por N frames antes de contar (reduz falsos positivos).  
Medir latência end-to-end (capture→inference→action). Logar P50/P95.  
Modularizar: inference/ (modelo + wrapper), postprocess.py.  

- Entregáveis

Código edge com modelo TFLite/PyTorch Mobile e instruções de conversão/empacotamento.  
Relatório de latência e recall em cenários controlados (lab).  

- Critérios de aceitação  

Recall alcança meta mínima (ex.: ≥85% em conjunto de validação lab).  
Latência média compatível com RNF (ex.: P95 ≤ 1s).  

## Fase 3 — Backend mínimo + Telemetria (Lab → integração)

- Objetivo: introduzir backend leve para centralizar eventos, preparar armazenamento e permitir updates.

O que fazer (técnico / código)  
Criar API mínima (ex.: FastAPI) com endpoint /events que recebe JSON e armazena em banco (inicialmente MySQL ou Mongo local).  
No edge, adicionar cliente HTTP/MQTT para envio de eventos/telemetria com backoff e buffering local (persistência em disco se off-line).  
Implementar autenticação simples (token) e TLS (self-signed em PoC).  
Incluir rota de healthcheck /health e métricas básicas (uptime, latest_ping).  
Preparar esquema DB (coleção events com campos: id_dispositivo, timestamp, tipo, confidence, bbox, latitude, longitude, firmware_version).  

- Entregáveis

Repo backend/ com API, instruções de deploy (docker-compose).  
Edge atualizado para enviar eventos ao backend.  

- Critérios de aceitação

Edge envia eventos com sucesso; backend persiste e retorna 2xx.  
Edge continua operando offline e reconcilia eventos ao reconectar.  

## Fase 4 — Piloto de Campo Controlado (1–3 pontos)

- Objetivo: validar comportamento real em campo, verificar robustez ambiental, energia e políticas de retenção / privacidade.

O que fazer (técnico / hardware / operação)  
Montagem física: gabinete IP65, fonte estável (ou bateria + painel solar), suporte para câmera.  
Instalar 1–3 unidades. Habilitar logs locais e telemetria.  
Coleta de ground-truth: gravar trechos (localmente, retenção curta) e anotar manualmente amostras para medir recall/precision.  
Ativar fallback local: se perda de conectividade com backend, edge aplica regras locais (acende sinal, continua logando).  
Introduzir mecanismo simples de OTA: servidor que entrega pacote assinado; edge verifica assinatura antes de aplicar. (Pode começar com script que baixa tar.gz e troca binário + restart.)  

- Código / mudanças

Integrar rotinas de criptografia básica em trânsito (TLS) e em repouso (encrypt logs if required).  
Implementar masked capture: por padrão não enviar vídeos; se enviá-los, aplicar blur/mascara de rostos/placas on-edge.  

- Entregáveis

Relatório do piloto: métricas reais (recall, precision, latência), problemas de instalação, logs de falhas.  
Processo de OTA testado em laboratório e aplicado em campo com rollback controlado.  

- Critérios de aceitação

Métricas no campo dentro das metas acordadas (recall/latência).  
Unidade opera autonomamente por janela mínima (p.ex. dias) e reconcilia eventos ao backend.  

## Fase 5 — Evolução para Detecção Avançada + Rastreamento (YOLO + Centroid Tracker)

- Objetivo: migrar para modelo mais preciso (YOLO nano/YOLOvX otimizado) e reintroduzir rastreamento com IDs (como no main.py).

O que fazer (técnico / código)

Escolha do modelo: usar versão compacta (YOLO-nano / YOLOv8n) e quantizar/otimizar para edge (TFLite int8 ou ONNX + OpenVINO/Coral/Jetson se disponível).

Adaptar o rastreador atual (classe RastreadorDeCentroides do main.py) à saída do novo modelo:

Conectar boxes e confidences do YOLO ao tracker;

Manter sets ids_contados_* como já no main.py.

Reintroduzir diferenciação por classe (carro=2, moto=3 ou conforme dataset), usar confidence threshold dinâmico (configurável por config.json).

Refatorar main.py para separar responsabilidades: edge_capture.py, edge_inference.py, tracker.py, uploader.py, actuator.py.

- Entregáveis

Versão do edge com YOLO otimizado + rastreador e integração com backend/MongoDB.

Scripts de conversão do modelo e documentação de como reproduzir builds.

- Critérios de aceitação

Detecta e rastreia múltiplos objetos com IDs persistentes; contagens por hora consolidadas.

Performance aceitável no hardware alvo (latência dentro do RNF).

## Fase 6 — Backend escalável (MongoDB, dashboards, observability)

- Objetivo: mover armazenamento para solução escalável (MongoDB/Timeseries), dashboards e monitoramento.

O que fazer (técnico / infra)

Migrar persistência de logs/contagens para MongoDB (ou outra DB adequada). Criar esquema de metadados por intervalo (hora) e por dispositivo.

Construir dashboard (React + Tailwind) ou usar Grafana para visualização: contagens por hora, health dos dispositivos, latência, taxa de falsos positivos.

Implementar observability: Prometheus metrics exporter no edge/backend, logs centralizados (ELK/Graylog) ou solução leve (filebeat → backend).

Automatizar deploys (Docker images para backend; pipeline CI que builds image edge quando necessário e assina para OTA).

- Entregáveis

Dashboard operacional; DB cluster (prod/dev); CI/CD e playbook de rollbacks.

- Critérios de aceitação

Dashboards mostram métricas reais com alertas configurados; DB armazena e serve queries de métricas com performance aceitável.

## Fase 7 — Escala e Operação

- Objetivo: preparar cadeia de suprimentos, manutenções, contratos, e planejar rollout descentralizado.

O que fazer (processo / ops)

Definir BOM final (custo por unidade) e SLA de manutenção.

Formular contrato de instalação e manutenção local.

Planejar estoques, peças de reposição, e processo de troca de unidade no campo.

Implementar playbook de incident response (como lidar com privacidade, falhas de segurança, recalls de firmware).

- Entregáveis

Playbook de implantação, contrato de manutenção, plano financeiro para escala.

- Critérios de aceitação

Capacidade operacional para implantar X unidades com processo definido (logística, suporte).

Migração prática no código (passo a passo técnico)

Modularize imediatamente o PoC em módulos (capture, detector, logger, config). Isso torna a troca de modelo e integração com backend trivial.

Adicionar config.json com parâmetros (thresholds, model_path, backend_url, device_id). Use argparse para overrides em runtime.

Substitua o detector: implemente uma interface Detector com métodos load_model(), infer(frame) → List[Detections]. Primeiro implement HaarDetector, depois TFLiteDetector, depois YOLODetector. O resto do código usa a interface.

Adapte o tracker: copie/integre a RastreadorDeCentroides (do main.py) como tracker.py. Garanta que Detections contenham (bbox, class_id, confidence, centroid) para compatibilidade.

Buffering e retries: antes de postar no backend, grave eventos em disco num arquivo/SQLite local; serviço que sincroniza em segundo plano quando houver rede.

```
OTA: começar com script update.sh que baixa artefato assinado (image.tar.gz.sig) e aplica; mais tarde evoluir para Mender/balena.

Tests: tenha unit tests para detector, tracker e uploader (simular frames).

Métricas e gates de promoção (usar para decidir subir para a próxima fase)

Recall (lab/field) ≥ 85%

Precision ≥ 75–80% (minimizar acionamentos desnecessários)

Latência (P95) ≤ 1s (ou meta RNF)

Uptime do device mínimo aceitável (definir SLA)

Capacidade de atualização OTA com rollback testado

Checklist mínimo para avançar entre fases

PoC → Lab: modularização + detector leve funcional

Lab → Backend: enviar eventos reliably + healthcheck

Backend → Pilot: hardware físico + coleta groundtruth + OTA básico

Pilot → Escala: observability, contratos de manutenção, BOM final e validações legais (LGPD)
```
