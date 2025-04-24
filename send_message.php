<?php
require_once 'config.php';

// Receber dados do frontend
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['user_id']) || !isset($data['message'])) {
    echo json_encode([
        'success' => false,
        'message' => 'ID do usuário e mensagem são obrigatórios'
    ]);
    exit;
}

$user_id = $data['user_id'];
$message = $data['message'];

// Inserir mensagem no banco de dados
$stmt = $conn->prepare("INSERT INTO messages (sender_id, message) VALUES (?, ?)");
$stmt->bind_param("is", $user_id, $message);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Mensagem enviada com sucesso'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao enviar mensagem: ' . $conn->error
    ]);
}

$stmt->close();
$conn->close();
?>