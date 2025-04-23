<?php
header('Content-Type: application/json');
require 'db.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['sender_id']) || !isset($data['receiver_email']) || !isset($data['content'])) {
    echo json_encode(['status' => 'error', 'message' => 'Dados incompletos']);
    exit;
}

$sender_id = $data['sender_id'];
$receiver_email = $data['receiver_email'];
$content = trim($data['content']);

if ($content === '') {
    echo json_encode(['status' => 'error', 'message' => 'Mensagem vazia']);
    exit;
}

// Busca o ID do usuário destinatário
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$receiver_email]);
$receiver = $stmt->fetch();

if (!$receiver) {
    echo json_encode(['status' => 'error', 'message' => 'Usuário de destino não encontrado']);
    exit;
}

$receiver_id = $receiver['id'];

// Insere a mensagem privada
try {
    $stmt = $pdo->prepare("INSERT INTO private_chats (sender_id, receiver_id, content) VALUES (?, ?, ?)");
    $stmt->execute([$sender_id, $receiver_id, $content]);

    echo json_encode(['status' => 'ok', 'message' => 'Mensagem enviada']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Erro: ' . $e->getMessage()]);
}
?>
