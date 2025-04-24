<?php
// Habilitar exibição de erros para depuração
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'config.php';

// Log para depuração
error_log("Requisição de login recebida");

// Obter dados brutos da requisição
$raw_data = file_get_contents("php://input");
error_log("Dados brutos recebidos: " . $raw_data);

// Receber dados do frontend
$data = json_decode($raw_data, true);
error_log("Dados decodificados: " . print_r($data, true));

if (!isset($data['email']) || !isset($data['password'])) {
    error_log("Email ou senha não fornecidos");
    echo json_encode([
        'success' => false,
        'message' => 'Email e senha são obrigatórios'
    ]);
    exit;
}

$email = $data['email'];
$password = $data['password'];

error_log("Tentativa de login para o email: " . $email);

// Buscar usuário pelo email
$stmt = $conn->prepare("SELECT id, email, password FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    error_log("Usuário não encontrado: " . $email);
    echo json_encode([
        'success' => false,
        'message' => 'Email ou senha incorretos'
    ]);
    exit;
}

$user = $result->fetch_assoc();
error_log("Usuário encontrado: " . print_r($user, true));

// Verificar senha
if (password_verify($password, $user['password'])) {
    error_log("Senha correta para o usuário: " . $email);
    // Remover a senha do objeto de usuário antes de enviar para o frontend
    unset($user['password']);
    
    echo json_encode([
        'success' => true,
        'message' => 'Login realizado com sucesso',
        'user' => $user
    ]);
} else {
    error_log("Senha incorreta para o usuário: " . $email);
    echo json_encode([
        'success' => false,
        'message' => 'Email ou senha incorretos'
    ]);
}

$stmt->close();
$conn->close();
?>