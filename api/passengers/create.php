<?php
// Definir cabeçalhos para resposta JSON
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Incluir conexão com banco de dados
include_once '../config/database.php';

// Obter conexão com o banco de dados
$database = new Database();
$db = $database->getConnection();

// Verificar se os dados foram enviados como JSON
$data = json_decode(file_get_contents("php://input"));

// Verificar se os campos obrigatórios foram preenchidos
if (!empty($data->nome) && !empty($data->telefone)) {
    // Preparar instrução SQL para inserir o passageiro
    $query = "INSERT INTO passengers (nome, telefone) VALUES (:nome, :telefone)";
    $stmt = $db->prepare($query);

    // Limpar dados
    $nome = htmlspecialchars(strip_tags($data->nome));
    $telefone = htmlspecialchars(strip_tags($data->telefone));

    // Vincular parâmetros
    $stmt->bindParam(":nome", $nome);
    $stmt->bindParam(":telefone", $telefone);

    // Executar a query
    if ($stmt->execute()) {
        http_response_code(201); // Código 201: Criado
        echo json_encode(["message" => "Passageiro cadastrado com sucesso."]);
    } else {
        http_response_code(503); // Código 503: Serviço Indisponível
        echo json_encode(["message" => "Não foi possível cadastrar o passageiro."]);
    }
} else {
    // Código 400: Requisição Inválida
    http_response_code(400);
    echo json_encode(["message" => "Dados incompletos."]);
}
