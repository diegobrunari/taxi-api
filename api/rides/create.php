<?php
// Incluir a configuração do banco de dados
include_once '../config/database.php';

// Criar uma nova conexão com o banco de dados
$database = new Database();
$conn = $database->getConnection();

// Verificar se a conexão foi bem-sucedida
if ($conn) {
    // Verificar se os dados foram enviados via POST
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Obter os dados do corpo da requisição
        $data = json_decode(file_get_contents("php://input"));

        // Verificar se o passenger_id, origem e destino foram fornecidos
        if (isset($data->passageiro_id) && isset($data->origem) && isset($data->destino)) {
            // Verificar se o passageiro existe
            $passengerQuery = "SELECT id FROM passengers WHERE id = :passageiro_id";
            $passengerStmt = $conn->prepare($passengerQuery);
            $passengerStmt->bindParam(':passageiro_id', $data->passageiro_id);
            $passengerStmt->execute();

            if ($passengerStmt->rowCount() > 0) {
                // Preparar a consulta SQL para inserir a corrida
                $query = "INSERT INTO rides (passageiro_id, status, origem, destino, data_hora_solicitacao) 
                          VALUES (:passageiro_id, 'Aguardando Motorista', :origem, :destino, NOW())";
                $stmt = $conn->prepare($query);

                // Bind os parâmetros
                $stmt->bindParam(':passageiro_id', $data->passageiro_id);
                $stmt->bindParam(':origem', $data->origem);
                $stmt->bindParam(':destino', $data->destino);

                // Executar a consulta
                if ($stmt->execute()) {
                    echo json_encode(["message" => "Corrida cadastrada com sucesso."]);
                } else {
                    echo json_encode(["message" => "Erro ao cadastrar corrida."]);
                }
            } else {
                echo json_encode(["message" => "Passageiro não encontrado."]);
            }
        } else {
            echo json_encode(["message" => "Dados incompletos."]);
        }
    } else {
        echo json_encode(["message" => "Método não suportado."]);
    }
} else {
    echo json_encode(["message" => "Erro de conexão."]);
}
