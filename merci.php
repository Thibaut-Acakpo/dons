<?php
require_once __DIR__ . '/includes/functions.php';

// --- Recherche par numéro (formulaire) : on identifie le donateur puis on recharge la page
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $numero = normalize_phone(post('numero'));
    if (throttle_blocked('lookup', 12, 900)) {
        flash('err', 'Trop de tentatives. Réessaie dans quelques minutes.');
    } elseif (!valid_phone($numero)) {
        flash('err', 'Numéro invalide : entre 8 à 15 chiffres.');
    } else {
        $d = find_donateur_by_phone($numero);
        if ($d) { $_SESSION['donateur_id'] = (int)$d['id']; }
        else {
            throttle_hit('lookup');
            flash('err', "Aucun donateur trouvé avec ce numéro. Tu peux faire un premier don depuis la page « Faire un don ».");
        }
    }
    redirect('merci.php');
}

$donateur = current_donateur();

// --- Don qui vient d'être enregistré (message de remerciement)
$nouveau = null;
$idNew = (int)get('don', 0);
if ($idNew) {
    $st = $pdo->prepare('SELECT d.*, dn.prenom, dn.nom FROM Dons d JOIN Donateur dn ON dn.id = d.id_donateur WHERE d.id_don = ?');
    $st->execute(array($idNew));
    $row = $st->fetch();
    $ok = $row && ((!empty($_SESSION['dons_ok']) && in_array($idNew, $_SESSION['dons_ok'])) || ($donateur && (int)$row['id_donateur'] === (int)$donateur['id']));
    if ($ok) { $nouveau = $row; }
}

// --- Historique (donateur identifié)
$dons = array(); $usages = array(); $totaux = array();
if ($donateur) {
    $st = $pdo->prepare('SELECT * FROM Dons WHERE id_donateur = ? ORDER BY date_don DESC, id_don DESC');
    $st->execute(array($donateur['id']));
    $dons = $st->fetchAll();
    foreach (types_don() as $t) { $totaux[$t] = array('n' => 0, 'somme' => 0.0); }
    foreach ($dons as $d) {
        if (isset($totaux[$d['type_don']])) { $totaux[$d['type_don']]['n']++; $totaux[$d['type_don']]['somme'] += (float)$d['montant']; }
    }
    $st = $pdo->prepare('SELECT r.*, d.type_don, d.detail_don FROM repartition r JOIN Dons d ON d.id_don = r.id_don
                         WHERE d.id_donateur = ? ORDER BY r.date_utilisation DESC, r.id_repartition DESC');
    $st->execute(array($donateur['id']));
    $usages = $st->fetchAll();
}

$pageTitle = $nouveau ? 'Merci !' : 'Suivre mes dons';
$nav = '';
require __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
  <span class="blob b1"></span><span class="blob b2"></span>
  <div class="wrap">
    <h1 class="rise"><?= $nouveau ? 'Merci de tout cœur !' : 'Suivre mes dons' ?></h1>
    <p class="rise" style="--d:.12s"><?= $nouveau ? 'Votre don est bien enregistré.' : 'Retrouvez l\'historique de vos dons et la façon dont ils ont été utilisés.' ?></p>
  </div>
</section>

<div class="wrap" style="max-width:900px;margin-top:-34px;position:relative;z-index:2">

  <?php if ($nouveau): ?>
    <div class="card center rise" data-confetti style="margin-bottom:24px">
      <div class="thanks-icon" aria-hidden="true"><?= svg_icon('heart') ?></div>
      <h2>Merci <?= e($nouveau['prenom']) ?> !</h2>
      <p>Votre don
        <?= $nouveau['type_don'] === 'Financier' ? 'de <strong>' . e(fcfa($nouveau['montant'])) . '</strong>' : '<strong>' . e(type_label($nouveau['type_don'])) . '</strong>' ?>
        du <?= e(date_fr($nouveau['date_don'])) ?> a bien été enregistré.</p>
      <?php if ($nouveau['type_don'] === 'Financier' && SITE_PAYMENT_INFO !== ''): ?>
        <div class="alert alert-info" style="text-align:left"><?= nl2br(e(SITE_PAYMENT_INFO)) ?></div>
      <?php elseif ($nouveau['type_don'] !== 'Financier'): ?>
        <p class="muted">Contactez-nous au <strong><?= e(SITE_PHONE) ?></strong> pour convenir de la remise de votre don.</p>
      <?php endif; ?>
      <p><a class="btn btn-sun" href="recu.php?id=<?= (int)$nouveau['id_don'] ?>" target="_blank" rel="noopener"><?= svg_icon('receipt') ?> Voir / imprimer mon reçu</a></p>
    </div>
  <?php endif; ?>

  <?php if (!$donateur): ?>
    <div class="card form-card narrow" style="margin-top:0">
      <h2>Consulter mes dons</h2>
      <form method="post">
        <?= csrf_field() ?>
        <div class="field"><label for="numero"><?= svg_icon('mobile') ?> Votre numéro de téléphone</label>
          <input class="input" id="numero" name="numero" type="tel" inputmode="tel" placeholder="Ex : 01 56 03 48 10" required></div>
        <button class="btn btn-block" type="submit">Voir mes dons</button>
      </form>
      <p class="center" style="margin:18px 0 0"><a href="faire_don.php">Pas encore donateur ? Faire un don</a></p>
    </div>
  <?php else: ?>
    <div class="card" style="margin-bottom:24px">
      <div style="display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;align-items:center">
        <div><h2 style="margin:0"><?= svg_icon('user') ?> Bonjour <?= e($donateur['prenom'] . ' ' . $donateur['nom']) ?></h2>
          <p class="muted" style="margin:.2em 0 0"><?= count($dons) ?> don<?= count($dons) > 1 ? 's' : '' ?> enregistré<?= count($dons) > 1 ? 's' : '' ?>.</p></div>
        <div style="display:flex;gap:10px;flex-wrap:wrap">
          <a class="btn btn-sun btn-sm" href="faire_don.php"><?= svg_icon('heart') ?> Nouveau don</a>
          <a class="btn btn-ghost btn-sm" href="faire_un_don.php?reset=1">Changer de numéro</a>
        </div>
      </div>
    </div>

    <?php if (!$dons): ?>
      <div class="card center"><p>Vous êtes inscrit comme donateur mais n'avez pas encore effectué de don.</p><a class="btn" href="faire_don.php">Faire mon premier don</a></div>
    <?php else: ?>
      <div class="grid g4" style="margin-bottom:24px">
        <?php foreach ($totaux as $t => $v): ?>
          <div class="card stat tilt" style="padding:20px 14px"><span class="ico"><?= type_icon($t) ?></span>
            <div class="num" style="font-size:1.5rem"><?= $t === 'Financier' ? e(fcfa($v['somme'])) : (int)$v['n'] ?></div>
            <div class="lbl"><?= e(type_label($t)) ?><?= $t === 'Financier' ? ' (' . (int)$v['n'] . ')' : '' ?></div></div>
        <?php endforeach; ?>
      </div>

      <h2><?= svg_icon('clipboard') ?> Historique de vos dons</h2>
      <div class="table-wrap" style="margin-bottom:28px"><table class="table">
        <thead><tr><th>Date</th><th>Type</th><th class="num">Montant</th><th>Détail</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($dons as $d): ?>
          <tr>
            <td><?= e(date_fr($d['date_don'])) ?></td>
            <td><span class="badge b-type"><?= type_icon($d['type_don']) ?> <?= e(type_label($d['type_don'])) ?></span></td>
            <td class="num"><?= e(montant_don($d)) ?></td>
            <td><?= e($d['detail_don']) ?></td>
            <td class="act"><a href="recu.php?id=<?= (int)$d['id_don'] ?>" target="_blank" rel="noopener"><?= svg_icon('receipt') ?> Reçu</a></td>
          </tr>
        <?php endforeach; ?>
        </tbody></table></div>

      <h2><?= svg_icon('receipt') ?> À quoi ont servi vos dons</h2>
      <?php if ($usages): ?>
        <div class="table-wrap"><table class="table">
          <thead><tr><th>Date</th><th>Usage</th><th class="num">Montant utilisé</th><th>Don concerné</th></tr></thead>
          <tbody>
          <?php foreach ($usages as $u): ?>
            <tr>
              <td><?= e(date_fr($u['date_utilisation'])) ?></td>
              <td><?= e($u['USA']) ?></td>
              <td class="num"><?= $u['type_don'] === 'Financier' ? e(fcfa($u['montant_utilise'])) : '—' ?></td>
              <td><?= e($u['type_don'] === 'Financier' ? 'Don financier' : type_label($u['type_don']) . ($u['detail_don'] ? ' — ' . str_cut($u['detail_don'], 40) : '')) ?></td>
            </tr>
          <?php endforeach; ?>
          </tbody></table></div>
      <?php else: ?>
        <div class="card center muted">Vos dons récents n'ont pas encore été répartis. Revenez bientôt : nous mettons à jour cette page à chaque utilisation.</div>
      <?php endif; ?>
    <?php endif; ?>
  <?php endif; ?>
  <p class="center" style="margin:30px 0 0"><a class="btn btn-ghost" href="index.php"><?= svg_icon('home') ?> Retour à l'accueil</a></p>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
