<h1 align="center">CORSA</h1>

<h4 align="center">Câmeras Operacionais de Reconhecimento e Segurança Automobilística</h4>

> Sistema para cidades inteligentes voltado para reduzir acidentes em cruzamentos estreitos e de baixa visibilidade, oferecendo uma alternativa mais acessível e eficiente ao semáforo tradicional.

---

## 📌 Descrição Geral

O **CORSA** (Câmeras Operacionais de Registro e Segurança Automobilística) é um sistema inovador voltado ao conceito de **cidades inteligentes**. Seu propósito é reduzir acidentes em cruzamentos de baixa visibilidade por meio de detecção automática, alerta visual e registro de eventos, proporcionando alternativa mais acessível e eficiente ao semáforo tradicional.

---

## 🧭 Justificativa

Em diversos contextos urbanos, cruzamentos muito fechados e sem controle de tráfego apresentam riscos para motoristas, ciclistas e pedestres. A instalação de semáforos convencionais nem sempre é economicamente viável ou eficiente, principalmente em vias de menor fluxo. O CORSA propõe uma solução tecnológica moderna e escalável para aumentar a segurança, reduzir custos e viabilizar cidades mais inteligentes e conectadas.

---

## ⚙️ Funcionamento

O sistema é composto por:

* **Sensores inteligentes** — detectam presença de veículos e pedestres nas interseções;
* **Câmeras de monitoramento** — registram ocorrências e apoiam auditoria/segurança;
* **Sinalização luminosa adaptativa** — aciona alertas visuais para indicar o momento seguro de travessia/fluxo;
* **Módulo de software central** — integra dados dos sensores/câmeras, registra eventos para análise e permite ajustes remotos pela gestão pública.

O fluxo básico de operação:

1. Sensores detectam aproximação/estacionamento de veículo ou fluxo de pedestres;
2. Dados são enviados ao módulo central em tempo real;
3. Algoritmos decidem o acionamento da sinalização luminosa adaptativa;
4. Evento é registrado no sistema.

---

## 🎯 Benefícios Esperados

* Redução de acidentes em cruzamentos de risco;
* Implantação mais econômica comparada a semáforos tradicionais;
* Maior segurança para pedestres e motoristas;
* Monitoramento contínuo para análise e otimização do tráfego;
* Apoio à política de cidades inteligentes e mobilidade sustentável.

---

## 🛠️ Escopo de Desenvolvimento de Software

O software do CORSA contempla:

1. **Integração em tempo real** com sensores e câmeras (protocolos e APIs);
2. **Processamento de dados** para detecção de movimento e identificação de situações de risco (event detection);
3. **Controle inteligente da sinalização** (lógica de ativação das luzes e temporizações adaptativas);
4. **Armazenamento de registros** (logs e eventos) para auditoria e estatísticas;
5. **Painel de gerenciamento** web para parametrização, visualização de eventos e dashboards para a administração municipal.

---

## 💻 Tecnologias Utilizadas

* **Python** — processamento de sensores/câmeras e lógica embarcada (Edge)
* **PHP** — backend e integração do painel web
* **MySQL** — banco de dados de eventos, logs e usuários
* **HTML5/CSS3/JavaScript (Tailwind)** — frontend do painel administrativo
* **MQTT/HTTPS** — protocolos de comunicação entre módulos

---

## 🏗️ Arquitetura

* **Edge:** Dispositivo embarcado (ex: Raspberry Pi) com sensores e câmeras, executando scripts Python para detecção e envio de eventos.
* **Gateway:** (opcional) Intermediário para comunicação entre múltiplos Edges e o backend, pode atuar como buffer para garantir resiliência de rede.
* **Backend:** Servidor principal em PHP responsável pelo recebimento, validação, armazenamento de eventos e gerenciamento do sistema.
* **Banco de Dados:** MySQL, armazena metadados, eventos, registros de sinalização, usuários e configurações.
* **Frontend:** Painel web desenvolvido em HTML5/CSS3/JS (Tailwind) para administração, visualização de eventos e geração de relatórios.

---

## 🔧 Instalação

> Exemplo mínimo para ambiente de desenvolvimento.

1. Clonar o repositório:

```bash
git clone https://github.com/RafaelCavallaroRossi/CORSA.git
cd CORSA
```

2. Instalar dependências Python (Edge):

```bash
pip install numpy
pip install paho-mqtt gpiozero
```

3. Configurar banco de dados MySQL (exemplo):

```sql
CREATE DATABASE corsa;
-- Siga o script em docs/db_schema.sql para criação de tabelas
```

4. Configurar backend (PHP):

```ini
Edite o arquivo config.php com as credenciais do banco e parâmetros do sistema.
```

5. Configurar Frontend:

- Acesse a pasta frontend/ e siga as instruções do README correspondente.

---

## 🚦 Uso e Parametrizações

* Ajuste sensibilidade dos sensores e zonas de detecção conforme a geometria do cruzamento;
* Defina políticas de acionamento (ex.: prioridade para pedestres em horário comercial);
* Configure retenção de vídeo e logs conforme legislação/local de privacidade.

---

## 📊 Telemetria, Privacidade e Conformidade

* Os registros podem conter imagens e dados sensíveis — recomenda-se criptografia em trânsito (TLS/HTTPS) e repouso, além de políticas claras de retenção e acesso;
* O sistema prevê mecanismos de anonimização/mascaramento de rostos e placas, caso exigido por lei (ex.: LGPD);
* Consulte a [Documentação de Entrega](./DocumentaçãoDeEntrega.md) para detalhes de privacidade e requisitos legais.

---

## 📃 Documentação e Referências

* [Documentação de Entrega](./DocumentaçãoDeEntrega.md) — requisitos, critérios de aceitação, arquitetura e escopo.
* [Estrutura Analítica do Projeto](./EstruturaAnaliticaDoProjeto.md) — fases e etapas de desenvolvimento.
* [Manual do Usuário e Documentação Técnica](./docs/).

---

## 🧪 Critérios de Aceitação e Testes

* Detecção correta (recall) ≥ 85% em condições variadas;
* Latência média de decisão ≤ 1 s;
* Estabilidade: recuperação automática de falhas simples;
* Procedimentos de atualização remota validados em ambiente de teste;
* Consulte a [Documentação de Entrega](./DocumentaçãoDeEntrega.md#4-testes-e-critérios-de-aceitação) para detalhes.

---

## 👥 Desenvolvido por

**Grupo Block-Brain** 🧠

---

## 📄 Licença

Este projeto está sob a **Licença FATEC**. Mais informações: [https://fatecitapira.cps.sp.gov.br/](https://fatecitapira.cps.sp.gov.br/)

---

## 📮 Contato

Para dúvidas, contribuições ou solicitações de implantação, entre em contato com o *Grupo Block-Brain* através do repositório GitHub ou do e‑mail: rafacavallarorossi132@gmail.com.

---

### 👉 Roadmap

* [ ] MVP — integração básica sensores ↔ backend ↔ painel;
* [ ] Testes de campo em cruzamentos pilotos;
* [ ] Otimizações de detecção por visão computacional;
* [ ] Integração com sistemas de gestão de trânsito municipal;
* [ ] Estudos de viabilidade econômica e expansão.

> Atualizado 13/10/2025
