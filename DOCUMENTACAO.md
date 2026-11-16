# TaskFlow — resumo e requisitos funcionais

## Resumo do sistema

TaskFlow é um sistema web de organização pessoal. O usuário cria uma conta, registra tarefas, define prioridade e prazo e atualiza o andamento de cada atividade. O administrador pode consultar, editar e excluir contas cadastradas.

O projeto foi desenvolvido em PHP, MySQL, HTML e CSS, sem framework. O acesso ao banco é feito com PDO e consultas preparadas. As senhas são armazenadas com `password_hash` e verificadas com `password_verify`.

## Requisitos funcionais

### Home
- **RF01:** Exibir apresentação do TaskFlow e opções de cadastro e login para visitantes.
- **RF02:** Para usuários autenticados, exibir saudação com o nome e resumo das tarefas.
- **RF03:** Exibir opções de navegação conforme o tipo de conta; Administração aparece somente para administradores.
- **RF04:** Permitir sair da conta e bloquear as telas restritas após o encerramento da sessão.

### Cadastro
- **RF05:** Receber nome, e-mail e senha, exigindo todos os campos.
- **RF06:** Validar o formato do e-mail e impedir e-mails duplicados.
- **RF07:** Exigir senha com pelo menos seis caracteres e armazená-la em formato hash.
- **RF08:** Criar novas contas com o tipo comum.

### Login
- **RF09:** Validar e-mail e senha usando os dados cadastrados.
- **RF10:** Direcionar usuários comuns para a home e administradores para o painel administrativo.
- **RF11:** Exibir mensagem genérica quando as credenciais forem inválidas.

### Perfil
- **RF12:** Permitir que o usuário altere seu nome e e-mail.
- **RF13:** Validar o e-mail e impedir que seja utilizado por outra conta.
- **RF14:** Permitir a troca de senha somente após conferir a senha atual e validar o tamanho da nova senha.

### Execução — Minhas tarefas
- **RF15:** Permitir acesso somente a usuários autenticados.
- **RF16:** Permitir cadastrar tarefas com título, descrição opcional, prioridade e prazo.
- **RF17:** Permitir alterar o status de uma tarefa entre pendente, em andamento e concluída.
- **RF18:** Permitir excluir tarefas e mostrar somente tarefas pertencentes ao usuário logado.
- **RF19:** Permitir filtrar tarefas por status.

### Redefinição de senha
- **RF20:** Receber e validar o e-mail informado.
- **RF21:** Conferir se o e-mail está cadastrado e registrar a solicitação no banco.
- **RF22:** Exibir uma confirmação da solicitação. Nesta versão acadêmica não há envio de e-mail nem alteração automática da senha.

### Área do administrador
- **RF23:** Permitir acesso somente a contas do tipo administrador.
- **RF24:** Listar usuários com ID, nome, e-mail, tipo e data de cadastro.
- **RF25:** Permitir editar nome, e-mail e tipo de conta.
- **RF26:** Permitir excluir usuários, impedindo que o administrador exclua a própria conta.

## Estrutura do banco

- `usuarios`: dados de acesso e tipo de conta.
- `tarefas`: atividades vinculadas ao usuário responsável.
- `redefinicoes_senha`: registro das solicitações de redefinição.

## Papéis do trio

- Desenvolvedor Full Stack: **[preencher nome]** — desenvolvimento do sistema, resumo e requisitos funcionais.
- QA Sênior: **[preencher nome]** — elaboração dos casos de teste.
- QA Júnior: **[preencher nome]** — execução dos testes e registro dos resultados.
