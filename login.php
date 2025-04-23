<?php
require 'db.php';

$data = json_decode(file_get_contents("php://input"), true);

$email = $data['email'];
$password = $data['password'];

$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($password, $user['password'])) {
    echo json_encode(["status" => "ok", "user_id" => $user['id']]);
} else {
    echo json_encode(["status" => "error", "message" => "Credenciais inválidas"]);
}
?>
