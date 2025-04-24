<?php
require_once 'config.php';

// Receber dados do frontend
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['email']) || !isset($data['password'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Email e senha são obrigatórios'
    ]);
    exit;
}

$email = $data['email'];
$password = $data['password'];

// Validar email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'success' => false,
        'message' => 'Email inválido'
    ]);
    exit;
}

// Verificar se o email já existe
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Este email já está cadastrado'
    ]);
    exit;
}

// Hash da senha
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Inserir novo usuário
$stmt = $conn->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
$stmt->bind_param("ss", $email, $hashed_password);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Usuário registrado com sucesso'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao registrar usuário: ' . $conn->error
    ]);
}

$stmt->close();
$conn->close();
?>