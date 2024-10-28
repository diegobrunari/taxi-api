<?php
// Incluir a configuração do banco de dados
include_once '../config/database.php';

// Criar uma nova conexão com o banco de dados
$database = new Database();
$conn = $database->getConnection();

// Verificar se a conexão foi bem-sucedida
if ($conn) {
    // Verificar se o ID da corrida foi passado como parâmetro de consulta
    if (isset($_GET['id'])) {
        $ride_id = $_GET['id'];

        // Preparar a consulta SQL para obter os detalhes da corrida
        $query = "SELECT * FROM rides WHERE id = :ride_id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':ride_id', $ride_id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $ride = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode($ride);
        } else {
            echo json_encode(["message" => "Corrida não encontrada."]);
        }
    } else {
        echo json_encode(["message" => "ID da corrida não fornecido."]);
    }
} else {
    echo json_encode(["message" => "Erro de conexão."]);
}
