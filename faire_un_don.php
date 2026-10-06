<?php
require_once __DIR__ . '/includes/functions.php';
if (get('reset') === '1') { unset($_SESSION['donateur_id']); }
$pageTitle = 'Faire un don';
$nav = '';
require __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
  <span class="blob b1"></span><span class="blob b2"></span>
  <div class="wrap"><h1 class="rise">Faire un don</h1><p class="rise" style="--d:.12s">Dites-nous simplement si vous êtes déjà donateur.</p></div>
</section>
<div class="wrap">
  <div class="grid g2 form-card" style="max-width:900px">
    <div class="card tilt" data-tilt="4">
      <h2>Je suis nouveau</h2>
      <p class="muted">Première fois parmi nous ? Enregistrez votre don en une minute : nom, prénom, numéro de téléphone et type de don.</p>
      <a class="btn btn-sun btn-block" href="faire_don.php">Oui, je suis nouveau donateur</a>
    </div>
    <div class="card tilt" data-tilt="4">
      <h2>Je suis déjà donateur</h2>
      <form action="rediriger_donateur.php" method="post">
        <?= csrf_field() ?>
        <div class="field">
          <label for="numero">Votre numéro de téléphone</label>
          <input class="input" id="numero" name="numero" type="tel" inputmode="tel" placeholder="Ex : 01 56 03 48 10" required>
        </div>
        <div class="field">
          <label for="action">Que souhaitez-vous faire ?</label>
          <select class="input" id="action" name="action" required>
            <option value="">— Choisissez —</option>
            <option value="nouveau_don">Faire un nouveau don</option>
            <option value="consulter_dons">Consulter mes dons</option>
          </select>
        </div>
        <button class="btn btn-block" type="submit">Continuer</button>
      </form>
    </div>
  </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
