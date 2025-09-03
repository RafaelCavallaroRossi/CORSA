# Documentação de Entrega

**Câmeras Operacionais de Registro e Segurança Automobilística**

---

## 1. Introdução

### 1.1 Objetivo do Documento

Este documento consolida todas as informações do projeto de desenvolvimento de códigos para câmeras que verificam o movimento de veículos, servindo como guia para stakeholders, equipe de desenvolvimento e usuários finais.

### 1.2 Escopo do Projeto

* **Câmeras projetadas para servir sob o dia, noite, chuva e outros efeitos climáticos;
* **Sensores de movimentos que analizam a aprocimação de veículos, com base no formato, mudança de proporção e da placa;
* **Lentes com visão noturna.

> **Obs.** Ainda está na fase de planejamento.

FIM

### 1.3 Visão Geral

A plataforma centraliza conteúdos e serviços, com:

* **Área do Administrador**: Gestão completa de usuários e turmas.
* **Área do Professor**: Gerenciamento de Aulas e Frequência.

---

## 2. Requisitos do Sistema

### 2.1 Requisitos Funcionais

1. **RF01: Gestão de Usuários e Autenticação**

   * Cadastro e login com níveis de acesso.
2. **RF02: Informações Acadêmicas**
   * Dados dos alunos.

3. **RF03: Checagem diária de Presença**
   * Acesso a frequência.

4. **RF04: Adicionamento de aulas a serem Ministradas**
   * Gestão de Aulas e Matérias.

### 2.2 Requisitos Não Funcionais

* **Performance**: Carregamento < 3s.
* **Compatibilidade**: Navegadores.
* **Responsividade**: Desktop, tablet, smartphone.
### 2.3 Restrições

* Sem aplicativo móvel nativo; foco em responsividade web.

---

## 3. Arquitetura do Sistema

### 3.1 Tecnologias

* **Frontend**: https://cdn.tailwindcss.com, https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css, CSS.
* **Backend**: PHP, JavaScript.
* **Banco de Dados**: MySQL.
* **Publicação**: GitHub.

## 4. Testes

* **Unitários**: Manual, Página à Página.
* **Integração**: Lotando o Banco e testando capacidades.
* **UI/UX**: Zoom.

---

## 5. Manutenção & Suporte

* Correção de bugs emergenciais.
* Atualizações futuras para outros PI's.

---

## 6. Riscos e Mitigações

| Risco                    | Mitigação                                      |
| ------------------------ | ---------------------------------------------- |
| Falhas de segurança      | Acessibilidade e Simplicidade de uso           |
| Performance insuficiente | Otimização e Responsividade                    |
| Baixa adoção             | Aderiu todas as características dos requisitos |

---

## 7. Anexos

* Talvez GitHub Pages.

---

## 8. Conclusão

Projeto entregue com 101% dos requisitos implementados e validado em testes de usabilidade.

---

Em desenvolvimento por: Grupo Block-Brain.

