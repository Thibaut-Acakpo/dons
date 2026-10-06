<?php
require_once __DIR__ . '/includes/functions.php';

$erreur = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $message = post('message');
    if (throttle_blocked('contact', 5, 600)) {
        $erreur = 'Tu as envoyé plusieurs messages à la suite. Réessaie dans quelques minutes.';
    } elseif (str_len($message) < 5) {
        $erreur = 'Ton message est trop court (5 caractères minimum).';
    } elseif (str_len($message) > 2000) {
        $erreur = 'Ton message est trop long (2000 caractères maximum).';
    } else {
        $idDonateur = !empty($_SESSION['donateur_id']) ? (int)$_SESSION['donateur_id'] : null;
        $st = $pdo->prepare("INSERT INTO encouragement (message, frequence, date_envoi, id_donateur) VALUES (?, 'unique', NOW(), ?)");
        $st->execute(array($message, $idDonateur));
        throttle_hit('contact');
        flash('ok', 'Votre message a bien été envoyé. Merci pour votre soutien !');
        redirect('contact.php');
    }
}

$pageTitle = 'Contact';
$nav = 'contact';
require __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
  <span class="blob b1"></span><span class="blob b2"></span>
  <div class="wrap"><h1 class="rise">Contactez l'orphelinat</h1><p class="rise" style="--d:.12s">Un mot d'encouragement, une question, une proposition : nous lisons chaque message.</p></div>
</section>
<div class="wrap">
  <div class="grid g2" style="max-width:960px;margin:-40px auto 70px;position:relative;z-index:2;align-items:start">
    <form class="card" method="post">
      <?= csrf_field() ?>
      <h2>Votre message</h2>
      <?php if ($erreur): ?><div class="alert alert-err" role="alert"><?= e($erreur) ?></div><?php endif; ?>
      <div class="field">
        <label for="message">Votre message</label>
        <textarea class="input" id="message" name="message" rows="7" maxlength="2000" required><?= old('message') ?></textarea>
      </div>
      <button class="btn btn-block" type="submit">Envoyer le message</button>
    </form>
    <aside class="card tilt" data-tilt="4">
      <h2>Nos coordonnées</h2>
      <p><?= svg_icon('mail') ?> <a href="mailto:<?= e(SITE_EMAIL) ?>"><?= e(SITE_EMAIL) ?></a></p>
      <p><?= svg_icon('phone') ?> <a href="tel:<?= e(preg_replace('/\s+/', '', SITE_PHONE)) ?>"><?= e(SITE_PHONE) ?></a></p>
      <p class="muted">Vous souhaitez remettre un don en nature ? Enregistrez-le d'abord sur le site, puis appelez-nous pour convenir d'un rendez-vous.</p>
      <a class="btn btn-sun" href="faire_un_don.php"><?= svg_icon('heart') ?> Faire un don</a>
    </aside>
  </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
