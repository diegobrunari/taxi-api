# Taxi API

Uma API simples em PHP para gerenciar corridas de táxi, permitindo o cadastro de passageiros, criação e atualização de corridas, e controle de status com cálculo automático do valor baseado no tempo.

## Pré-requisitos

- [Docker](https://docs.docker.com/get-docker/)
- [Docker Compose](https://docs.docker.com/compose/install/)

## Estrutura do Projeto

```plaintext
TAXI-API/
│
├── docker-compose.yml
├── Dockerfile
├── src/
│   ├── index.php
│   ├── config/
│   │   └── database.php
│   ├── api/
│   │   ├── passengers/
│   │   │   └── create.php
│   │   ├── rides/
│   │   │   ├── create.php
│   │   │   ├── update_status.php
│   │   │   └── get_ride.php
│   └── ...
└── README.md
```

# Configuração e Execução

## Clone o repositório

```bash
git clone <URL-do-repositorio>
cd TAXI-API
```

## No arquivo docker-compose.yml, configure as variáveis de ambiente necessárias

Inicie o ambiente Docker

```bash
docker-compose up -d
```

## Verifique se o container foi iniciado corretamente

```bash
docker ps
```

Acesse o phpMyAdmin em http://localhost:8081 para gerenciar o banco de dados, usando as credenciais definidas no ``docker-compose.yml``.

## Crie as tabelas passengers e rides no banco de dados com a seguinte estrutura:

```bash
CREATE TABLE passengers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    telefone VARCHAR(15) NOT NULL
);

CREATE TABLE rides (
    id INT AUTO_INCREMENT PRIMARY KEY,
    passageiro_id INT,
    motorista_id INT,
    status ENUM('Aguardando Motorista', 'Em Andamento', 'Finalizada') DEFAULT 'Aguardando Motorista',
    origem VARCHAR(255),
    destino VARCHAR(255),
    data_hora_solicitacao DATETIME DEFAULT CURRENT_TIMESTAMP,
    data_hora_inicio DATETIME NULL,
    data_hora_fim DATETIME NULL,
    valor DECIMAL(10, 2) DEFAULT 0.00,
    FOREIGN KEY (passageiro_id) REFERENCES passengers(id)
);
```

## Endpoints Disponíveis

```plaintext
1. Cadastro de Passageiros
   - URL: http://localhost:8080/api/passengers/create.php
   - Método: POST
   - Corpo da Requisição:
     {
         "nome": "Nome do Passageiro",
         "telefone": "123456789"
     }

2. Criar uma Corrida
   - URL: http://localhost:8080/api/rides/create.php
   - Método: POST
   - Corpo da Requisição:
     {
         "passageiro_id": 1,
         "origem": "Endereço de Origem",
         "destino": "Endereço de Destino"
     }
   - Regras:
     - A corrida só pode ser criada se o passageiro_id existir.

3. Atualizar o Status de uma Corrida e Calcular o Valor
   - URL: http://localhost:8080/api/rides/update_status.php
   - Método: PATCH
   - Corpo da Requisição para Iniciar Corrida:
     {
         "ride_id": 1,
         "status": "Em Andamento",
         "motorista_id": 2
     }
   - Corpo da Requisição para Finalizar Corrida:
     {
         "ride_id": 1,
         "status": "Finalizada"
     }
   - Regras:
     - Alterar o status para "Em Andamento" somente se estiver em "Aguardando Motorista" e com motorista_id informado.
     - Alterar o status para "Finalizada" somente se estiver em "Em Andamento".
     - Calcula o valor da corrida em R$1,03 por minuto enquanto estiver "Em Andamento".

4. Obter Detalhes de uma Corrida
   - URL: http://localhost:8080/api/rides/get_ride.php?id=<ride_id>
   - Método: GET
   - Exemplo: http://localhost:8080/api/rides/get_ride.php?id=1
```

## Testando com o Postman

```plaintext
1. Abra o Postman e crie uma nova requisição para cada um dos endpoints listados acima.

2. Envie requisições conforme o tipo de método (POST, PATCH, GET).

3. Verifique as respostas para garantir que o sistema esteja funcionando corretamente.

Exemplo de Requisição para Listar Corrida:
   - Método: GET
   - URL: http://localhost:8080/api/rides/get_ride.php?id=1
   - Resposta:
     {
         "id": 1,
         "passageiro_id": 1,
         "motorista_id": 2,
         "status": "Finalizada",
         "origem": "Endereço de Origem",
         "destino": "Endereço de Destino",
         "data_hora_solicitacao": "2023-10-15 12:00:00",
         "data_hora_inicio": "2023-10-15 12:10:00",
         "data_hora_fim": "2023-10-15 12:40:00",
         "valor": "30.90"
     }
```

## Finalizando o Projeto e Problemas Comuns

```plaintext
Para encerrar os containers Docker:
   docker-compose down
   Isso irá interromper e remover os containers.

Problemas Comuns:
1. Erro 404 - Not Found:
   - Verifique o nome e o caminho dos arquivos na estrutura do projeto.
   - Certifique-se de que o docker-compose.yml e os arquivos PHP estão corretamente configurados.

2. Erro de Conexão com o Banco de Dados:
   - Certifique-se de que o MySQL está configurado corretamente no docker-compose.yml e no database.php.
   - Reinicie os containers com docker-compose down e docker-compose up -d.
```

## Contribuição

Sinta-se à vontade para abrir issues ou fazer pull requests para melhorias.