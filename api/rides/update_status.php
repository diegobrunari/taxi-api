<?php
// Incluir a configuração do banco de dados
include_once '../config/database.php';

// Criar uma nova conexão com o banco de dados
$database = new Database();
$conn = $database->getConnection();

// Verificar se a conexão foi bem-sucedida
if ($conn) {
    // Verificar se os dados foram enviados via PATCH
    if ($_SERVER['REQUEST_METHOD'] == 'PATCH') {
        // Obter os dados do corpo da requisição
        $data = json_decode(file_get_contents("php://input"));

        // Verificar se o ride_id e o novo status foram fornecidos
        if (isset($data->ride_id) && isset($data->status)) {
            // Preparar a consulta SQL para obter a corrida
            $query = "SELECT status, data_hora_inicio FROM rides WHERE id = :ride_id";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':ride_id', $data->ride_id);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $ride = $stmt->fetch(PDO::FETCH_ASSOC);

                // Alterar o status conforme as regras
                if ($ride['status'] == 'Aguardando Motorista' && isset($data->motorista_id)) {
                    // Alterar para "Em Andamento" e definir data_hora_inicio
                    $updateQuery = "UPDATE rides SET status = 'Em Andamento', motorista_id = :motorista_id, data_hora_inicio = NOW() WHERE id = :ride_id";
                    $updateStmt = $conn->prepare($updateQuery);
                    $updateStmt->bindParam(':motorista_id', $data->motorista_id);
                    $updateStmt->bindParam(':ride_id', $data->ride_id);

                    if ($updateStmt->execute()) {
                        echo json_encode(["message" => "Status alterado para 'Em Andamento'."]);
                    } else {
                        echo json_encode(["message" => "Erro ao atualizar o status."]);
                    }
                } elseif ($ride['status'] == 'Em Andamento' && $data->status == 'Finalizada') {
                    // Calcular o valor da corrida
                    $valorInicial = 5.00; //R$5,00 valor inicial da corrida
                    $valorPorMinuto = 3.03; // R$3,03 por minuto
                    $dataHoraInicio = new DateTime($ride['data_hora_inicio']);
                    $dataHoraAtual = new DateTime(); // Hora atual

                    // Calcular a diferença em minutos
                    $intervalo = $dataHoraInicio->diff($dataHoraAtual);
                    $minutos = ($intervalo->h * 60) + $intervalo->i; // Total de minutos
                    $valorTotal = $minutos * $valorPorMinuto + $valorInicial; // Valor total da corrida

                    // Alterar para "Finalizada"
                    $updateQuery = "UPDATE rides SET status = 'Finalizada', data_hora_fim = NOW(), valor = :valor WHERE id = :ride_id";
                    $updateStmt = $conn->prepare($updateQuery);
                    $updateStmt->bindParam(':valor', $valorTotal);
                    $updateStmt->bindParam(':ride_id', $data->ride_id);

                    if ($updateStmt->execute()) {
                        echo json_encode(["message" => "Status alterado para 'Finalizada'.", "valor" => number_format($valorTotal, 2, ',', '.')]);
                    } else {
                        echo json_encode(["message" => "Erro ao atualizar o status."]);
                    }
                } else {
                    echo json_encode(["message" => "Transição de status inválida."]);
                }
            } else {
                echo json_encode(["message" => "Corrida não encontrada."]);
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
