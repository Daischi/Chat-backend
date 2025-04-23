<?php
header('Content-Type: application/json');
require 'db.php';

// Lê os dados do corpo da requisição
$data = json_decode(file_get_contents('php://input'), true);

// Verifica se recebeu os dados necessários
if (!isset($data['user_id']) || !isset($data['content'])) {
    echo json_encode(['status' => 'error', 'message' => 'Dados incompletos']);
    exit;
}

$user_id = $data['user_id'];
$content = trim($data['content']);

// Verifica se a mensagem não está vazia
if ($content === '') {
    echo json_encode(['status' => 'error', 'message' => 'Mensagem vazia']);
    exit;
}

// Insere no banco de dados
try {
    $stmt = $pdo->prepare("INSERT INTO messages (user_id, content) VALUES (?, ?)");
    $stmt->execute([$user_id, $content]);

    echo json_encode(['status' => 'ok', 'message' => 'Mensagem enviada']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Erro ao enviar: ' . $e->getMessage()]);
}
?>
