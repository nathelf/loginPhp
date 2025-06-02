<?php
require 'config.php';
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if ($name && $email && $password) {
        $hash = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        try {
            $stmt->execute([$name, $email, $hash]);
            echo "<script>alert('Usuário cadastrado!');</script>";
        } catch (PDOException $e) {
            echo "<script>alert('Erro: " . $e->getMessage() . "');</script>";
        }
    } else {
        echo "<script>alert('Preencha todos os campos!');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastro</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
    <h1>Cadastrar</h1>
    <form method="post">
        <input type="text" name="name" placeholder="Seu nome" required>
        <input type="email" name="email" placeholder="Seu email" required>
        <input type="password" name="password" placeholder="Senha" required>
        <button type="submit">Cadastrar</button>
    </form>
    <a href="login.php">Já tem conta? Entrar</a>
</div>
</body>
</html>
