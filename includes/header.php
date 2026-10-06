<?php
/** En-tête du site public. Variables attendues : $pageTitle, $nav (clé du menu actif) */
require_once __DIR__ . '/functions.php';
$pageTitle = isset($pageTitle) ? $pageTitle : SITE_NAME;
$nav       = isset($nav) ? $nav : '';
$bodyClass = isset($bodyClass) ? $bodyClass : '';
$menu = array(
    'home'      => array('index.php', 'Accueil'),
    'besoins'   => array('besoins.php', 'Nos besoins'),
    'activites' => array('voir_activites.php', 'Nos enfants en action'),
    'contact'   => array('contact.php', 'Contact'),
);
?><!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($pageTitle) ?> — <?= e(SITE_NAME) ?></title>
<meta name="description" content="<?= e(isset($pageDesc) ? $pageDesc : "Soutenez les enfants de l'" . SITE_NAME . " : faites un don, suivez son utilisation en toute transparence.") ?>">
<meta name="theme-color" content="#1b5e9b">
<script>document.documentElement.classList.add('js')</script>
<link rel="stylesheet" href="assets/css/style.css?v=<?= filemtime(__DIR__ . '/../assets/css/style.css') ?>">
</head>
<body class="<?= e($bodyClass) ?>">
<a class="skip" href="#main">Aller au contenu</a>
<header class="topbar" id="topbar">
  <div class="wrap topbar-in">
    <a class="brand" href="index.php" aria-label="<?= e(SITE_NAME) ?> — accueil">
      <span class="brand-mark"><svg width="24" height="24" viewBox="0 0 24 24" fill="#fff" aria-hidden="true"><path d="M12 21s-7.5-4.6-9.6-9.2C.9 8.4 2.9 5 6.2 5c2 0 3.4 1.1 4.3 2.5h3C14.4 6.1 15.8 5 17.8 5c3.3 0 5.3 3.4 3.8 6.8C19.5 16.4 12 21 12 21z"/></svg></span>
      <span class="brand-text"><strong><?= e(SITE_SHORT) ?></strong><small>Orphelinat</small></span>
    </a>
    <button class="nav-toggle" type="button" aria-label="Ouvrir le menu" aria-expanded="false" data-nav-toggle><?= svg_icon('menu') ?></button>
    <nav class="nav" id="nav" aria-label="Navigation principale">
      <?php foreach ($menu as $k => $m): ?>
        <a href="<?= e($m[0]) ?>" class="<?= $nav === $k ? 'on' : '' ?>"<?= $nav === $k ? ' aria-current="page"' : '' ?>><?= e($m[1]) ?></a>
      <?php endforeach; ?>
      <a class="btn btn-sun btn-sm" href="faire_un_don.php"><?= svg_icon('heart') ?> Faire un don</a>
    </nav>
  </div>
</header>
<?php flash_render(); ?>
<main id="main">
