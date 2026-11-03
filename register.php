<?php
require __DIR__ . '/includes/data.php';

$v = ['name' => '', 'email' => '', 'phone' => ''];
$errors = [];
$ok = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $v['name']  = post_str('name');
    $v['email'] = post_str('email');
    $v['phone'] = post_str('phone');
    $pass  = is_string($_POST['password'] ?? null)  ? $_POST['password']  : '';
    $pass2 = is_string($_POST['password2'] ?? null) ? $_POST['password2'] : '';

    $len = mb_strlen($v['name']);
    if ($len < 2 || $len > 100) {
        $errors['name'] = 'Аты-жөні 2–100 таңба болуы керек';
    }
    if (!filter_var($v['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Электрондық пошта дұрыс емес';
    }
    if (!preg_match('/^\+?[0-9\s()-]{10,20}$/', $v['phone'])) {
        $errors['phone'] = 'Телефон нөмірін дұрыс енгізіңіз';
    }
    if (strlen($pass) < 6) {
        $errors['password'] = 'Кемінде 6 таңба енгізіңіз';
    }
    if ($pass !== $pass2) {
        $errors['password2'] = 'Құпиясөздер сәйкес емес';
    }

    $ok = !$errors;
}

$title = 'Тіркелу';
require __DIR__ . '/includes/header.php';
?>

    <form class="form-box" method="post">
      <h1>Тіркелу</h1>
      <p class="details">Жаңа аккаунт жасап, көлік брондауды бастаңыз.</p>

      <label for="name">Аты-жөніңіз</label>
      <input id="name" name="name" value="<?= e($v['name']) ?>" placeholder="Аселя Ерназарова">
      <span class="error"><?= e($errors['name'] ?? '') ?></span>

      <label for="email">Электрондық пошта</label>
      <input id="email" name="email" type="email" value="<?= e($v['email']) ?>" placeholder="name@example.com">
      <span class="error"><?= e($errors['email'] ?? '') ?></span>

      <label for="phone">Телефон</label>
      <input id="phone" name="phone" type="tel" value="<?= e($v['phone']) ?>" placeholder="+7 700 000 00 00">
      <span class="error"><?= e($errors['phone'] ?? '') ?></span>

      <label for="password">Құпиясөз</label>
      <input id="password" name="password" type="password" placeholder="Кемінде 6 таңба">
      <span class="error"><?= e($errors['password'] ?? '') ?></span>

      <label for="password2">Құпиясөзді қайталаңыз</label>
      <input id="password2" name="password2" type="password">
      <span class="error"><?= e($errors['password2'] ?? '') ?></span>

      <button class="form-button" type="submit">Тіркелу</button>

      <?php if ($ok): ?>
        <p class="success">Деректер дұрыс. Дерекқорға сақтау 11-аптада қосылады.</p>
      <?php endif; ?>

      <p class="alt">Аккаунтыңыз бар ма? <a href="login.php">Кіру</a></p>
    </form>

<?php require __DIR__ . '/includes/footer.php'; ?>