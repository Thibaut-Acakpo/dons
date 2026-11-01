<?php
/** En-tête de l'espace admin. Variables : $adminTitle, $adminNav, $adminSub (facultatif). */
require_once __DIR__ . '/functions.php';
require_admin();
$adminTitle = isset($adminTitle) ? $adminTitle : 'Tableau de bord';
$adminNav   = isset($adminNav) ? $adminNav : '';
$links = array(
    'Pilotage' => array(
        'dash'      => array('admin_dashboard.php', 'home', 'Tableau de bord'),
        'dons'      => array('admin_dons.php', 'gift', 'Tous les dons'),
        'donateurs' => array('admin_donateurs.php', 'users', 'Donateurs'),
        'messages'  => array('admin_messages.php', 'message', 'Messages reçus'),
    ),
    'Besoins' => array(
        'besoins'   => array('manage_besoins.php', 'clipboard', 'Liste des besoins'),
        'add_b'     => array('add_besoin.php', 'plus', 'Ajouter un besoin'),
    ),
    'Répartitions' => array(
        'repart'    => array('view_repartition.php', 'share', 'Voir les répartitions'),
        'add_r'     => array('add_repartition.php', 'plus', 'Ajouter une répartition'),
    ),
);
?><!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex,nofollow">
<meta name="theme-color" content="#0c3556">
<title><?= e($adminTitle) ?> — Administration</title>
<script>document.documentElement.classList.add('js')</script>
<link rel="stylesheet" href="assets/css/style.css?v=<?= filemtime(__DIR__ . '/../assets/css/style.css') ?>">
</head>
<body class="admin-page">
<div class="adm">
  <aside class="side" id="side" aria-label="Menu d'administration">
    <div class="logo">
      <span class="brand-mark"><svg width="24" height="24" viewBox="0 0 24 24" fill="#fff" aria-hidden="true"><path d="M12 21s-7.5-4.6-9.6-9.2C.9 8.4 2.9 5 6.2 5c2 0 3.4 1.1 4.3 2.5h3C14.4 6.1 15.8 5 17.8 5c3.3 0 5.3 3.4 3.8 6.8C19.5 16.4 12 21 12 21z"/></svg></span>
      <div><strong><?= e(SITE_SHORT) ?></strong><small>Administration</small></div>
    </div>
    <?php foreach ($links as $groupe => $items): ?>
      <h5><?= e($groupe) ?></h5>
      <?php foreach ($items as $k => $l): ?>
        <a href="<?= e($l[0]) ?>" class="<?= $adminNav === $k ? 'on' : '' ?>" <?= $adminNav === $k ? 'aria-current="page"' : '' ?>><span class="nav-icon"><?= svg_icon($l[1]) ?></span><?= e($l[2]) ?></a>
      <?php endforeach; ?>
    <?php endforeach; ?>
    <h5>Compte</h5>
    <a href="index.php" target="_blank" rel="noopener"><span class="nav-icon"><?= svg_icon('globe') ?></span>Voir le site</a>
    <a href="logout.php" data-confirm="Voulez-vous vraiment vous déconnecter ?" data-confirm-title="Déconnexion" data-confirm-label="Se déconnecter"><span class="nav-icon"><?= svg_icon('unlock') ?></span>Se déconnecter</a>
  </aside>

  <button class="side-backdrop" type="button" data-side-backdrop aria-label="Fermer le menu" tabindex="-1"></button>

  <main class="adm-main" id="main-content">
    <header class="adm-top">
      <div class="adm-heading">
        <h1><?= e($adminTitle) ?></h1>
        <?php if (!empty($adminSub)): ?><p class="sub"><?= e($adminSub) ?></p><?php endif; ?>
      </div>
      <button type="button" class="btn btn-ghost btn-sm side-toggle" data-side-toggle aria-controls="side" aria-expanded="false" aria-label="Ouvrir le menu">
        <?= svg_icon('menu') ?> <span>Menu</span>
      </button>
    </header>
    <?php
    if (!empty($_SESSION['_flash'])) {
        foreach ($_SESSION['_flash'] as $f) { echo '<div class="alert alert-' . e($f[0]) . '" role="status">' . e($f[1]) . '</div>'; }
        unset($_SESSION['_flash']);
    }
    ?>
