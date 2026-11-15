<?php
require __DIR__ . '/includes/data.php';
require __DIR__ . '/includes/db.php';
require_login();

$stmt = $pdo->prepare('SELECT full_name, email, phone, created_at FROM users WHERE id = ?');
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Аккаунт дерекқордан өшірілген болса, сессияны жабамыз
if (!$user) {
    header('Location: logout.php');
    exit;
}

$title = 'Профиль';
require __DIR__ . '/includes/header.php';
?>

    <div class="form-box">
      <h1>Профиль</h1>
      <p><b>Аты-жөні:</b> <?= e($user['full_name']) ?></p>
      <p><b>Электрондық пошта:</b> <?= e($user['email']) ?></p>
      <p><b>Телефон:</b> <?= e($user['phone']) ?></p>
      <p><b>Тіркелген күні:</b> <?= e(date('d.m.Y', strtotime($user['created_at']))) ?></p>
      <button class="form-button" onclick="location.href='logout.php'">Шығу</button>
    </div>

<?php require __DIR__ . '/includes/footer.php'; ?>