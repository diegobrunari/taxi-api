<?php

define('HOST', getenv('HOST'));
define('DBNAME', getenv('DBNAME'));
define('USERNAME', getenv('USERNAME'));
define('PASSWORD', getenv('PASSWORD'));

class Database
{
    // Configurações de conexão
    private $host = HOST;
    private $dbname = DBNAME;
    private $username = USERNAME;
    private $password = PASSWORD;
    public $conn;

    // Método para obter a conexão com o banco de dados
    public function getConnection()
    {
        $this->conn = null; // Inicializa a conexão como nula
        try {
            // Cria uma nova conexão PDO
            $this->conn = new PDO("mysql:host={$this->host};dbname={$this->dbname}", $this->username, $this->password);
            // Configura o modo de erro do PDO para exceções
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            // Exibe uma mensagem de erro caso a conexão falhe
            echo "Erro de conexão: " . $exception->getMessage();
        }
        return $this->conn; // Retorna a conexão
    }
}
