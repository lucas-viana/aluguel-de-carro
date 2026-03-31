# Sistema Basico de Aluguel de Veiculos (PHP Puro)

Aplicacao web simples para uma empresa de aluguel de carros, com controle de usuarios, veiculos e alugueis.

## Tecnologias

- PHP 8.1+
- MySQL 8+
- HTML + CSS + JavaScript (frontend)
- Bootstrap 5 (via CDN)

## Funcionalidades

- Cadastro de usuarios com validacao de CPF, e-mail, telefone e data.
- Cadastro de veiculos com validacao de placa e status disponivel/alugado.
- Criacao de aluguel somente com veiculo disponivel.
- Finalizacao de aluguel liberando o veiculo automaticamente.
- Dashboard com contadores e ultimos alugueis.

## Validacoes implementadas

### Frontend

- Campos obrigatorios com HTML5.
- Validacao de CPF e placa por JavaScript.
- Validacao de intervalo de datas no cadastro de aluguel.
- Mascaras para CPF/telefone/placa no input.

### Backend

- Sanitizacao e validacao completa em PHP.
- Validacao real de CPF por algoritmo.
- Validacao de placa no padrao Mercosul e antigo.
- Validacao de datas e forma de pagamento.
- Regras de negocio para impedir aluguel de veiculo indisponivel.
- Uso de prepared statements (PDO) para evitar SQL Injection.

## Estrutura

- index.php: dashboard
- usuarios.php: cadastro/listagem/exclusao de usuarios
- veiculos.php: cadastro/listagem/controle de disponibilidade
- alugueis.php: criacao e finalizacao de alugueis
- config/database.php: conexao PDO
- lib/helpers.php: funcoes utilitarias
- lib/validators.php: validadores de entrada
- database/schema.sql: script de criacao do banco
- database/seed.sql: carga de dados de exemplo

## Como executar

1. Ajuste as credenciais de banco se necessario:

   - DB_HOST (padrao: 127.0.0.1)
   - DB_PORT (padrao: 3306)
   - DB_NAME (padrao: aluguel_veiculos)
   - DB_USER (padrao: root)
   - DB_PASS (padrao definido em config/database.php)
   - DB_AUTO_SEED (padrao: 1)

   O sistema usa variaveis de ambiente e, se nao existir, usa os valores padrao acima.

2. Inicie o servidor PHP apontando para a pasta do projeto:

   ```bash
   php -S localhost:8000 -t "C:\\Users\\lucas\\OneDrive\\Documents\\PROJETO E DESENVOLVIMENTO DE SOFTWARE\\aluguel-de-veiculos"
   ```

   Se a porta 8000 estiver ocupada, use outra porta, por exemplo 8004.

3. Abra no navegador:

   ```
   http://localhost:8000
   ```

## Inicializacao automatica do banco

- Na primeira requisicao, a aplicacao tenta criar o banco e todas as tabelas automaticamente.
- Se DB_AUTO_SEED estiver ativo (valor padrao 1), os dados demo de database/seed.sql sao carregados automaticamente quando as tabelas estao vazias.
- Para subir sem dados demo, defina DB_AUTO_SEED=0.

## Observacoes

- O usuario do MySQL precisa ter permissao para CREATE DATABASE e CREATE TABLE para o bootstrap automatico.
- Para producao, recomenda-se usar servidor web (Apache/Nginx) e variaveis de ambiente seguras.
