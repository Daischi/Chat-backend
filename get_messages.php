<?php
header('Content-Type: application/json');
require 'db.php';

try {
    // Busca todas as mensagens e o email do remetente
    $stmt = $pdo->query("
        SELECT messages.id, users.email, messages.content, messages.created_at
        FROM messages
        JOIN users ON messages.user_id = users.id
        ORDER BY messages.created_at ASC
    ");

    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'status' => 'ok',
        'messages' => $messages
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Erro ao buscar mensagens: ' . $e->getMessage()
    ]);
}
?>
