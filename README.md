# CORSA

**Câmeras Operacionais de Registro e Segurança Automobilística**

> Sistema para cidades inteligentes voltado para reduzir acidentes em cruzamentos estreitos e de baixa visibilidade, oferecendo uma alternativa mais acessível e eficiente ao semáforo tradicional.

---

## 📌 Descrição Geral

O **CORSA** (Câmeras Operacionais de Registro e Segurança Automobilística) é um sistema inovador voltado ao conceito de **cidades inteligentes**. Seu propósito é reduzir acidentes em cruzamentos de ruas estreitas e de baixa visibilidade, por meio de um sistema de detecção e sinalização adaptativa, de baixo custo e escalável.

## 🧭 Justificativa

Em diversos contextos urbanos, cruzamentos muito fechados e sem controle de tráfego apresentam riscos para motoristas, ciclistas e pedestres. A instalação de semáforos convencionais nem sempre é viável (custo, baixo fluxo, infraestrutura). O CORSA propõe uma solução alternativa e econômica, mantendo padrões de segurança e permitindo implantação rápida em pontos críticos.

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
4. Evento é registrado (vídeo/metadados) para auditoria e estatísticas.

## 🎯 Benefícios Esperados

* Redução de acidentes em cruzamentos de risco;
* Implantação mais econômica comparada a semáforos tradicionais;
* Maior segurança para pedestres e motoristas;
* Monitoramento contínuo para análise e otimização do tráfego;
* Apoio à política de cidades inteligentes e mobilidade sustentável.

## 🛠️ Escopo de Desenvolvimento de Software

O software do CORSA contempla:

1. **Integração em tempo real** com sensores e câmeras (protocolos e APIs);
2. **Processamento de dados** para detecção de movimento e identificação de situações de risco (event detection);
3. **Controle inteligente da sinalização** (lógica de ativação das luzes e temporizações adaptativas);
4. **Armazenamento de registros** (logs, metadados, trechos de vídeo) para auditoria e estatísticas;
5. **Painel de gerenciamento** web para parametrização, visualização de eventos e dashboards para a administração municipal.

---

## 💻 Tecnologias Utilizadas

* **PHP** (com PDO para acesso seguro ao banco de dados)
* **MySQL**
* **HTML5** / **CSS3**
* **JavaScript** (interatividade e validações)
* **Bootstrap** *(opcional, para responsividade e componentes visuais)*

---

## 🏗️ Arquitetura (sugestão)

* **Edge**: sensores + câmeras + microcontrolador/mini-PC responsável por pré-processamento e envio de eventos;
* **Gateway**: concentrador local (opcional) que agrega dados e envia ao servidor central;
* **Backend**: API em PHP responsável por ingestão, processamento e armazenamento;
* **Banco de Dados**: MySQL para registros e estatísticas;
* **Frontend**: painel web responsivo para administração (HTML/CSS/JS + Bootstrap);
* **Armazenamento de mídia**: diretório/objet storage para trechos de vídeo e imagens (pode ser local ou em nuvem).

---

## 🔧 Instalação (exemplo rápido)

> Exemplo mínimo para ambiente de desenvolvimento.

1. Clonar o repositório:

```bash
git clone https://github.com/SEU_USUARIO/corsa.git
cd corsa
```

2. Instalar dependências (se houver composer):

```bash
composer install
```

3. Criar banco de dados MySQL e importar esquema (ex.: `schema.sql`):

```sql
CREATE DATABASE corsa_db;
-- Importar schema.sql via cliente ou phpMyAdmin
```

4. Configurar variáveis de ambiente / arquivo de configuração (`.env` ou `config.php`):

```ini
DB_HOST=127.0.0.1
DB_NAME=corsa_db
DB_USER=root
DB_PASS=seu_password
```

5. Subir servidor local (ex.: Apache/PHP) e acessar o painel de administração.

---

## 🚦 Uso e Parametrizações

* Ajuste sensibilidade dos sensores e zonas de detecção conforme a geometria do cruzamento;
* Defina políticas de acionamento (ex.: prioridade para pedestres em horário comercial);
* Configure retenção de vídeo e logs conforme legislação/local de privacidade.

---

## 📊 Telemetria e Privacidade

* Os registros podem conter imagens de usuários — recomenda-se criptografia em trânsito e repouso, além de políticas claras de retenção e acesso;
* Conformidade com legislações locais (por exemplo, LGPD) deverá ser considerada na fase de implantação;
* Mecanismos de anonimização/mascaramento de rostos podem ser implementados quando necessário.

---

## 👥 Desenvolvido por

**Grupo Block-Brain** 🧠

---

## 📄 Licença

Este projeto está sob a **Licença FATEC**. Mais informações: [https://fatecitapira.cps.sp.gov.br/](https://fatecitapira.cps.sp.gov.br/)

---

## 📮 Contato

Para dúvidas, contribuições ou solicitações de implantação, entre em contato com o *Grupo Block-Brain* através do repositório GitHub ou do e‑mail do projeto.

---

### 👉 Roadmap (sugestões)

* [ ] MVP — integração básica sensores ↔ backend ↔ painel;
* [ ] Testes de campo em cruzamentos pilotos;
* [ ] Otimizações de detecção por visão computacional;
* [ ] Integração com sistemas de gestão de trânsito municipal;
* [ ] Estudos de viabilidade econômica e expansão.
