<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
    <h1>Olá, <?= htmlspecialchars($_SESSION['user_name']) ?>!</h1>
    <a href="logout.php">Sair</a>
</div>
</body>
</html>
