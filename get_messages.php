<?php
require_once 'config.php';

// Buscar todas as mensagens do chat global
$query = "SELECT m.id, m.sender_id, u.email as sender_email, m.message, m.created_at 
          FROM messages m 
          JOIN users u ON m.sender_id = u.id 
          ORDER BY m.created_at ASC";

$result = $conn->query($query);

if (!$result) {
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao buscar mensagens: ' . $conn->error
    ]);
    exit;
}

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

echo json_encode([
    'success' => true,
    'messages' => $messages
]);

$conn->close();
?>