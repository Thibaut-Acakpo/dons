<?php
require_once __DIR__ . '/includes/functions.php';

if (is_admin()) { redirect('admin_dashboard.php'); }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $email    = post('email');
    $password = isset($_POST['password']) ? (string)$_POST['password'] : '';

    if (throttle_blocked('admin_login', 5, 600)) {
        $error = 'Trop de tentatives échouées. Réessaie dans 10 minutes.';
    } else {
        $okEmail = hash_equals(strtolower(ADMIN_EMAIL), strtolower($email));
        $okPass  = password_verify($password, ADMIN_PASSWORD_HASH);
        if ($okEmail && $okPass) {
            session_regenerate_id(true);
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_last'] = time();
            redirect('admin_dashboard.php');
        }
        throttle_hit('admin_login');
        $error = 'Identifiants incorrects.';
    }
}
?><!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex,nofollow">
<title>Connexion administrateur — <?= e(SITE_NAME) ?></title>
<script>document.documentElement.classList.add('js')</script>
<link rel="stylesheet" href="assets/css/style.css?v=<?= filemtime(__DIR__ . '/assets/css/style.css') ?>">
</head>
<body>
<div class="login">
  <span class="blob b1"></span><span class="blob b2"></span>
  <div class="login-card tilt rise" data-tilt="3">
    <div style="display:grid;place-items:center;margin-bottom:14px"><span class="brand-mark"><svg width="24" height="24" viewBox="0 0 24 24" fill="#fff" aria-hidden="true"><path d="M12 21s-7.5-4.6-9.6-9.2C.9 8.4 2.9 5 6.2 5c2 0 3.4 1.1 4.3 2.5h3C14.4 6.1 15.8 5 17.8 5c3.3 0 5.3 3.4 3.8 6.8C19.5 16.4 12 21 12 21z"/></svg></span></div>
    <h1>Connexion administrateur</h1>
    <p class="sub"><?= e(SITE_NAME) ?></p>
    <?php if ($error): ?><div class="alert alert-err" role="alert"><?= e($error) ?></div><?php endif; ?>
    <form method="post" autocomplete="on">
      <?= csrf_field() ?>
      <div class="field"><label for="email">Adresse e-mail</label><input class="input" id="email" name="email" type="email" autocomplete="username" value="<?= old('email') ?>" required autofocus></div>
      <div class="field"><label for="password">Mot de passe</label><input class="input" id="password" name="password" type="password" autocomplete="current-password" required></div>
      <button class="btn btn-block" type="submit">Se connecter</button>
    </form>
    <p class="center" style="margin:20px 0 0"><a href="index.php"><?= svg_icon('arrow-left') ?> Retour à l'accueil</a></p>
  </div>
</div>
<script src="assets/js/app.js?v=<?= filemtime(__DIR__ . '/assets/js/app.js') ?>" defer></script>
</body>
</html>
