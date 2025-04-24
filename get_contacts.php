<?php
require_once 'config.php';

// Buscar todos os usuários para a lista de contatos
$query = "SELECT id, email FROM users ORDER BY email ASC";

$result = $conn->query($query);

if (!$result) {
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao buscar contatos: ' . $conn->error
    ]);
    exit;
}

$contacts = [];
while ($row = $result->fetch_assoc()) {
    $contacts[] = $row;
}

echo json_encode([
    'success' => true,
    'contacts' => $contacts
]);

$conn->close();
?>