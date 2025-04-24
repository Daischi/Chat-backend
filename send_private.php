<?php
require_once 'config.php';

// Receber dados do frontend
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['user_id']) || !isset($data['receiver_email']) || !isset($data['message'])) {
    echo json_encode([
        'success' => false,
        'message' => 'ID do usuário, email do destinatário e mensagem são obrigatórios'
    ]);
    exit;
}

$user_id = $data['user_id'];
$receiver_email = $data['receiver_email'];
$message = $data['message'];

// Buscar ID do destinatário pelo email
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $receiver_email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Destinatário não encontrado'
    ]);
    exit;
}

$receiver = $result->fetch_assoc();
$receiver_id = $receiver['id'];

// Inserir mensagem privada no banco de dados
$stmt = $conn->prepare("INSERT INTO private_messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
$stmt->bind_param("iis", $user_id, $receiver_id, $message);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Mensagem privada enviada com sucesso'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao enviar mensagem privada: ' . $conn->error
    ]);
}

$stmt->close();
$conn->close();
?>