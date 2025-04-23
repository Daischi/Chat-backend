<?php
require 'db.php';

$data = json_decode(file_get_contents("php://input"), true);

$email = $data['email'];
$password = password_hash($data['password'], PASSWORD_DEFAULT);

$stmt = $pdo->prepare("INSERT INTO users (email, password) VALUES (?, ?)");

try {
    $stmt->execute([$email, $password]);
    echo json_encode(["status" => "ok"]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
