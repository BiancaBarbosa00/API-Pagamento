# Projeto API Pagamento

Projeto de integração a gateway de pagamento, utilizando PHP, Eloquent ORM e Guzzle.

## Sumário

- [Descrição](#descrição)
- [Requisitos](#requisitos)
- [Instalação](#instalação)
- [Configuração](#configuração)
- [Estrutura do Projeto](#estrutura-do-projeto)
- [Como Usar](#como-usar)
- [Scripts SQL](#scripts-sql)
- [Licença](#licença)

## Descrição

Este projeto envia dados de pagamento para um gateway externo via API, realizando consultas ao banco de dados e atualizando o status dos pedidos conforme o retorno da transação.

## Requisitos

- PHP >= 8.2
- Composer
- Banco de dados compatível (PostgreSQL)
- Extensões PHP necessárias para Eloquent e Guzzle

## Instalação

1. Clone o repositório:
    ```sh
    git clone https://github.com/BiancaBarbosa00/API-Pagamento
    cd API-Pagamento
    ```

2. Instale as dependências:
    ```sh
    composer install
    ```

## Configuração

1. Copie o arquivo `.env.example` para `.env`:
    ```sh
    cp .env.example .env
    ```

2. Preencha as variáveis de ambiente no arquivo `.env` com as informações do seu banco de dados e credenciais do gateway.

Exemplo:
```
DB_DRIVER=pgsql
DB_HOST=localhost
DB_DBNAME=seubanco
DB_USERNAME=usuario
DB_PASSWORD=senha

BASEURI=https://apiinterna.ecompleto.com.br
ENDPOINT=exams/processTransaction
TOKEN=seu_token
```

## Estrutura do Projeto

- [`public/index.php`](public/index.php): Pagina inicial da aplicação. Carrega variáveis de ambiente, inicializa o banco e executa o processamento.
- [`src/DB/Database.php`](src/DB/Database.php): Inicializa a conexão com o banco de dados usando Eloquent.
- [`app.php`](app.php): Lógica principal de consulta de pedidos, envio para o gateway e atualização do status.
- [`sql/`](sql/): Scripts SQL para criação e popularização das tabelas do BD.
- `.env.example`: Exemplo de configuração de ambiente.
- `composer.json`: Dependências do projeto.

## Como Usar

1. Importe os scripts SQL da pasta [`sql/`](sql/) no seu banco de dados.
2. Certifique-se de que o banco de dados está configurado e acessível.
3. Execute o projeto via servidor embutido do PHP ou configure seu servidor web apontando para a pasta `public`:
    ```sh
    php -S localhost:8000 -t public
    ```
4. Acesse `http://localhost:8000` para iniciar o processamento dos pedidos.

## Scripts SQL

A pasta [`sql/`](sql/) contém scripts para criação das tabelas necessárias:
- [`clientes.sql`](sql/clientes.sql)
- [`formas_pagamento.sql`](sql/formas_pagamento.sql)
- [`gateways.sql`](sql/gateways.sql)
- [`lojas_gateway (1).sql`](sql/lojas_gateway%20(1).sql)
- [`pedido_situacao.sql`](sql/pedido_situacao.sql)
- [`pedidos_pagamentos.sql`](sql/pedidos_pagamentos.sql)
- [`pedidos.sql`](sql/pedidos.sql)
