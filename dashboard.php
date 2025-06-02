<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require 'config.php';

// Atualiza contador de visitas apenas 1x por sessão
if (!isset($_SESSION['visited_dashboard'])) {
    $stmt = $pdo->prepare("UPDATE users SET visit_count = visit_count + 1 WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $_SESSION['visited_dashboard'] = true;
}

// Pega todos os usuários para exibir na dashboard
$users = $pdo->query("SELECT name, email, visit_count FROM users ORDER BY id DESC")->fetchAll();

// Pega avatar e email do usuário logado
$stmt = $pdo->prepare("SELECT avatar, email FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$currentUser = $stmt->fetch();
$_SESSION['user_email'] = $currentUser['email'];
$_SESSION['avatar'] = $currentUser['avatar'] ?? null;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="container dashboard">
    <h1>🎉 Bem-vindo, <?= htmlspecialchars($_SESSION['user_name']) ?>!</h1>
    <?php if ($_SESSION['avatar']): ?>
        <img src="uploads/avatars/<?= htmlspecialchars($_SESSION['avatar']) ?>" alt="Avatar" style="width: 80px; border-radius: 50%; margin: 10px 0;">
    <?php endif; ?>
    <p>Você está autenticado com sucesso.</p>
    <button id="openProfileModal" class="btn-profile">Editar Perfil</button>

    <!-- MODAL DE EDIÇÃO DE PERFIL -->
    <div id="profileModal" class="modal hidden">
        <div class="modal-content">
            <span id="closeModal" class="close">&times;</span>
            <h2>Editar Perfil</h2>
            <form id="profileForm" enctype="multipart/form-data" method="post" action="profile-update.php">
                <input type="text" name="name" placeholder="Nome" value="<?= htmlspecialchars($_SESSION['user_name']) ?>" required>
                <input type="email" name="email" placeholder="Email" value="<?= htmlspecialchars($_SESSION['user_email']) ?>" required>
                <input type="password" name="current_password" placeholder="Senha atual" required>
                <input type="password" name="new_password" placeholder="Nova senha (opcional)">
                <input type="file" name="avatar" accept="image/*">
                <button type="submit">Salvar</button>
            </form>
        </div>
    </div>

    <h2>👥 Usuários Cadastrados</h2>
    <div class="cards">
        <?php foreach ($users as $user): ?>
            <div class="card">
                <h3><?= htmlspecialchars($user['name']) ?></h3>
                <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
                <p><strong>Visitas:</strong> <?= $user['visit_count'] ?></p>
            </div>
        <?php endforeach; ?>
    </div>

    <h2 style="margin-top: 2rem;">📊 Gráfico de Visitas</h2>
    <canvas id="visitasChart" width="400" height="200"></canvas>

    <form action="logout.php" method="post">
        <button class="logout-btn" type="submit">Sair</button>
    </form>
</div>

<script>
    const users = <?= json_encode($users) ?>;
    if (users.length > 0) {
        const ctx = document.getElementById('visitasChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: users.map(u => u.name),
                datasets: [{
                    label: 'Visitas à Dashboard',
                    data: users.map(u => u.visit_count),
                    backgroundColor: '#007bff',
                    borderColor: '#0056b3',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }
</script>
<script src="js/modal.js"></script>
</body>
</html>
