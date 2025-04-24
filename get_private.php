<?php
require_once 'config.php';

// Receber dados do frontend
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['user_id']) || !isset($data['receiver_email'])) {
    echo json_encode([
        'success' => false,
        'message' => 'ID do usuário e email do destinatário são obrigatórios'
    ]);
    exit;
}

$user_id = $data['user_id'];
$receiver_email = $data['receiver_email'];

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

// Buscar mensagens privadas entre os dois usuários
$query = "SELECT pm.id, pm.sender_id, s.email as sender_email, 
                 pm.receiver_id, r.email as receiver_email, 
                 pm.message, pm.created_at 
          FROM private_messages pm 
          JOIN users s ON pm.sender_id = s.id 
          JOIN users r ON pm.receiver_id = r.id 
          WHERE (pm.sender_id = ? AND pm.receiver_id = ?) 
             OR (pm.sender_id = ? AND pm.receiver_id = ?) 
          ORDER BY pm.created_at ASC";

$stmt = $conn->prepare($query);
$stmt->bind_param("iiii", $user_id, $receiver_id, $receiver_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

echo json_encode([
    'success' => true,
    'messages' => $messages
]);

$stmt->close();
$conn->close();
?>