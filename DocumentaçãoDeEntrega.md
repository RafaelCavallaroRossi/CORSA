# Documentação de Entrega

**Câmeras Operacionais de Registro e Segurança Automobilística — CORSA**

---

## 1. Introdução

### 1.1 Objetivo do Documento

Consolidar informações técnicas e funcionais necessárias para entrega da solução CORSA, servindo como referência para stakeholders, equipe de desenvolvimento, testes e implantação.

### 1.2 Escopo do Projeto

O projeto cobre o desenvolvimento e validação de um sistema de detecção e sinalização para cruzamentos de baixa visibilidade, incluindo:

* Hardware de bordo (câmeras com visão noturna, sensores de presença, controladores embarcados e sinalização luminosa);
* Software embarcado para leitura de sensor, lógica de acionamento e logging;
* Painel web de administração para parametrizações, visualização de eventos e relatórios;

> **Observação:** Projeto em fase de planejamento e prototipagem; esta documentação define requisitos e critérios de aceitação para serem desenvolvidos.

### 1.3 Visão Geral do Funcionamento

1. O(s) sensor(es) e a(s) câmera(s) monitoram a aproximação em tempo real;
2. O microcomputador embarcado executa pré-processamento e envia eventos ao backend;
3. O backend aplica regras/IA para confirmar evento e decide acionamento das luzes de alerta;
4. O acionamento é registrado com metadados (timestamp + id do ponto).

### 1.4 Público-alvo

Equipes técnicas pequenas, laboratórios de prototipagem e prefeituras interessadas em projetos-piloto.

---

## 2. Requisitos do Sistema

### 2.1 Requisitos Funcionais (RF)

* **RF01 — Operação 24/7:** As câmeras e o software devem operar continuamente, com mecanismos automáticos de reinício/recuperação.
* **RF02 — Detecção de Aproximação:** Detectar veículos e pedestres antes do cruzamento (zona configurável) e classificar o tipo de objeto (veículo, pedestre, ciclista).
* **RF03 — Acionamento de Sinalização Luminosa:** Emitir alerta visual em faces do cruzamento conforme regras configuráveis (prioridade, temporização).
* **RF04 — Painel de Gestão:** Interface web para parametrização (sensibilidade, zonas, políticas de acionamento), consulta de eventos e dashboards.

### 2.2 Requisitos Não Funcionais (RNF)

* **RNF01 — Performance:** Latência de tomada de decisão (da detecção ao acionamento) <= 1 segundo em condições normais.
* **RNF02 — Disponibilidade:** Disponibilidade almejada >= 99% para o serviço crítico.
* **RNF03 — Segurança:** Comunicação autenticada e criptografada (TLS); controle de acesso ao painel administrativo.
* **RNF04 — Privacidade:** Impossibilitar a coleta de dados; retenção curta; criptografia em repouso; possibilidade de mascaramento de rostos/placas quando exigido por lei.
* **RNF05 — Escalabilidade:** Arquitetura capaz de suportar múltiplos pontos implantados com processamento centralizado ou distribuído.
* **RNF06 — Robustez Ambiental:** Hardware projetado para operar em variações de temperatura, umidade e chuva.

### 2.3 Restrições

 — O escopo atual **não** abrange proteção física contra vandalismo (reforços mecânicos poderão ser especificados em fases posteriores);  
 — Não há integração mandatória com semáforos municipais na fase inicial;   
 — integrações futuras serão estudadas.

---

## 3. Componentes Principais

### 3.1 Software Recomendado

* **Edge (MVP):** Linguagem: Python (bibliotecas RPi.GPIO, gpiozero, paho-mqtt se necessário);
* **Comunicação:** Local (sem rede): funciona de forma autônoma. Com rede: HTTPS POST simples para servidor ou MQTT para telemetria leve;
* **Backend:** PHP + MySQL simples ou apenas upload de logs por HTTPS, painel opcional apenas para visualização de logs e saúde dos dispositivos; 
* **Banco de Dados:** MySQL para metadados e estatísticas;
* **Frontend:** Painel web (HTML5/CSS3/JS + Tailwind) para administração, dashboards e relatórios.

### 3.2 Hardware Recomendado

* **Opção padrão:** Raspberry Pi 4 (ou modelo Lite) + case vedado;
Sensor ultrassônico (HC-SR04) ou sensor radar (HB100) para detecção de aproximação;
LED de alta intensidade em gabinete à prova d'água;
Fonte 12V/5V estável; cabo blindado para alimentar a sinalização.

* **Opção mais econômica:** Arduino Uno / Nano (mais barato; menos potência);
Sensor PIR/IR para movimento;
LED simples.
* **Opção robusta:** Raspberry Pi + câmera (PiCam) para gravação de trechos (somente testes);
Gabinete com grau de proteção IP65; suporte para painel solar e bateria.

> Notas: escolha sensores conforme o tipo de cruzamento (ultrassom para distâncias curtas; radar para melhor resistência a poeira/chuva).

### 3.3 Fluxo de Dados (resumido)

Edge → (evento) → Backend (validação/IA) → Decisão → Acionamento (Edge) + Registro no DB → Dashboard

---

## 4. Testes e Critérios de Aceitação

### 4.1 Tipos de Testes

* **Unitários:** Cobertura para módulos críticos (detecção, comunicação, regras de acionamento).
* **Integração:** Testes entre edge ↔ backend ↔ armazenamento; simulação de perda de conectividade e falhas.
* **Testes de Campo:** Instalação piloto em cruzamentos; medir taxa de detecção correta, latencia média e impacto nos incidentes.
* **UI/UX:** Validação do painel.

### 4.2 Critérios de Aceitação

* Detecção correta (recall) ≥ 85% em condições variadas;
* Latência média de decisão ≤ 1 s;
* Estabilidade: recuperação automaticamente de travamentos simples;
* Procedimentos de atualização remota validados em ambiente de teste.

---

## 5. Entregáveis

* Código fonte do backend e frontend em repositório GitHub;
* Plano de testes e relatórios de aceitação do piloto;
* Documentação técnica (instalação, operação, manutenção) e manual do usuário.

---

## 6. Manutenção & Suporte

* Suporte para correções críticas (SLA a definir);
* Atualizações regulares (segurança e desempenho);
* Monitoramento e alertas para falhas e degradação de performance;
* Registro de versões e changelog no repositório.

---

## 7. Riscos e Mitigações

| Risco                                         | Impacto | Mitigação                                                                              |
| --------------------------------------------- | ------: | -------------------------------------------------------------------------------------- |
| Condições ambientais adversas                 |    Alto | Seleção de hardware com IP adequado; testes de estresse ambiental |
| Problemas de privacidade / conformidade legal |    Alto | Políticas de retenção; anonimização; assessoria jurídica para conformidade (ex.: LGPD) |
| Energia instável            |   Alto | UPS local simples ou painel solar com bateria em locais sem rede confiável |
| Baixa aceitação da comunidade                 |   Médio | Programas piloto, campanhas de comunicação e métricas que comprovem eficácia |

---

## 8. Observações Finais e Status

* Estado atual: **Planejamento / Protótipo** — 0% dos requisitos implementados (documentação e definição de requisitos concluidas);
* Próximo passo recomendado: desenvolvimento do MVP com foco em validação de detecção e latência;
* Para a implantação em larga escala, elaborar estudo de custo/benefício e plano de manutenção física dos equipamentos.

---

**Em desenvolvimento por:** Grupo Block‑Brain
