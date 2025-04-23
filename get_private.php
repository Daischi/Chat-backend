<?php
header('Content-Type: application/json');
require 'db.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['user1_id']) || !isset($data['user2_email'])) {
    echo json_encode(['status' => 'error', 'message' => 'Dados incompletos']);
    exit;
}

$user1_id = $data['user1_id'];
$user2_email = $data['user2_email'];

// Pega ID do segundo usuário
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$user2_email]);
$user2 = $stmt->fetch();

if (!$user2) {
    echo json_encode(['status' => 'error', 'message' => 'Usuário não encontrado']);
    exit;
}

$user2_id = $user2['id'];

// Busca as mensagens entre os dois usuários (ida e volta)
$stmt = $pdo->prepare("
    SELECT sender_id, receiver_id, content, created_at
    FROM private_chats
    WHERE (sender_id = ? AND receiver_id = ?)
       OR (sender_id = ? AND receiver_id = ?)
    ORDER BY created_at ASC
");
$stmt->execute([$user1_id, $user2_id, $user2_id, $user1_id]);

$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'status' => 'ok',
    'messages' => $messages
]);
?>
