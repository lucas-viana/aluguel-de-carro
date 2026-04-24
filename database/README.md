# Configuração do Banco de Dados

## Passo 1: Criar o banco e as tabelas

Execute o SQL em um cliente MySQL:

```sql
mysql -u root -p < database/schema.sql
```

## Passo 2: Importar dados de teste (opcional)

```sql
mysql -u root -p < database/seed.sql
```

## Passo 3: Popular as senhas dos usuários

Execute o script PHP para hashear e atualizar as senhas:

```bash
php database/setup-passwords.php
```

## Credenciais de Teste

Após executar `setup-passwords.php`, use estas credenciais para fazer login:

| Email | Senha |
|-------|-------|
| lucas.almeida@email.com | senha123 |
| mariana.costa@email.com | senha456 |
| rafael.souza@email.com | senha789 |
| camila.pereira@email.com | senha000 |
| bruno.martins@email.com | senha111 |
| ana.lima@email.com | senha222 |

## Arquivos

- **schema.sql** - Cria as tabelas do banco (usuarios, veiculos, alugueis)
- **seed.sql** - Popula com dados de teste (6 usuários, 8 veículos, 5 aluguéis)
- **setup-passwords.php** - Hasheia e configura as senhas dos usuários

## Variáveis de Ambiente

Configure em `config/database.php` ou via variáveis de sistema:

```
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=aluguel_veiculos
DB_USER=root
DB_PASS=353742Ap$
```
