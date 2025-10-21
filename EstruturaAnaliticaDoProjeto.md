# Estrutura Analítica do Projeto - CORSA (Câmeras Operacionais de Reconhecimento e Segurança Automobilística)

### 1. Iniciação

1.1 Levantamento de Requisitos Técnicos e Funcionais  
1.2 Análise de Viabilidade Técnica e Econômica  
1.3 Definição do Escopo e Objetivos do Sistema  
1.4 Aprovação do Projeto e Termo de Abertura  

### 2. Planejamento

2.1 Elaboração do Cronograma de Desenvolvimento e Testes  
2.2 Análise e Gestão de Riscos (ambientais, técnicos e legais)  
2.3 Definição do Plano de Comunicação e Relatórios de Progresso  
2.4 Planejamento de Recursos (hardware, software e equipe)  

### 3. Análise e Design

3.1 Definição de Requisitos Funcionais e Não Funcionais  
3.2 Modelagem da Arquitetura do Sistema (Edge, Backend e Dashboard)  
3.3 Design de Interfaces do Painel Web  
3.3.1 Wireframes e Layouts dos Módulos  
3.3.2 Validação com Usuários (Prefeituras e Técnicos)  
3.4 Definição dos Protocolos de Comunicação (HTTP/MQTT)  

### 4. Desenvolvimento

4.1 Módulo de Captura e Detecção (Python - Edge)  
4.1.1 Configuração de Câmeras e Sensores  
4.1.2 Processamento de Imagem e Detecção de Movimento  
4.1.3 Registro de Logs Locais e Envio de Eventos  
4.2 Módulo de Backend e API (PHP/MySQL)  
4.2.1 Recebimento e Armazenamento de Dados  
4.2.2 Controle de Acesso e Segurança (TLS e autenticação)  
4.3 Módulo de Dashboard (Frontend Web)  
4.3.1 Cadastro e Gerenciamento de Câmeras  
4.3.2 Visualização de Eventos e Logs  
4.3.3 Geração de Relatórios e Estatísticas  
4.4 Integração entre Módulos  
4.4.1 Edge ↔ Backend  
4.4.2 Backend ↔ Dashboard  
4.4.3 Banco de Dados Centralizado  

### 5. Testes

5.1 Testes Unitários (Python e API)  
5.2 Testes de Integração (Edge ↔ Backend ↔ Dashboard)  
5.3 Testes de Interface e Usabilidade (UI/UX)  
5.4 Testes de Campo e Validação Real  
5.5 Correções e Ajustes  
5.5.1 Funcionais (detecção, registro, latência)  
5.5.2 Visuais/Interface (painel e relatórios)  
5.5.3 Revalidação Pós-Correção  

### 6. Implantação

6.1 Montagem Física e Configuração do Equipamento (Edge)  
6.2 Treinamento de Usuários (técnicos e operadores)  
6.3 Carga Inicial e Sincronização de Dados  
6.4 Teste de Funcionamento no Local (fase piloto)  

### 7. Encerramento

7.1 Documentação Final do Projeto (técnica e de uso)  
7.2 Avaliação com Stakeholders e Relatório de Desempenho  
7.3 Entrega Formal e Encerramento Administrativo  

* Suporte e Manutenção (Etapa Futuro/Opcional)  

  * Suporte Técnico Inicial (30 dias pós-implantação)  
  * Correção de Bugs e Atualizações de Segurança  
  * Planejamento de Versões Futuras e Expansão  
