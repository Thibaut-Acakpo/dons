<?php
require_once __DIR__ . '/includes/functions.php';
require_admin();

// Totaux financiers et indicateurs généraux.
$totalFin    = (float)$pdo->query("SELECT COALESCE(SUM(montant),0) FROM Dons WHERE type_don = 'Financier'")->fetchColumn();
$utilise     = (float)$pdo->query('SELECT COALESCE(SUM(montant_utilise),0) FROM repartition')->fetchColumn();
$nbDons      = (int)$pdo->query('SELECT COUNT(*) FROM Dons')->fetchColumn();
$nbDonateurs = (int)$pdo->query('SELECT COUNT(*) FROM Donateur')->fetchColumn();
$nbBesoins   = (int)$pdo->query('SELECT COUNT(*) FROM Besoins')->fetchColumn();
$nbMessages  = (int)$pdo->query('SELECT COUNT(*) FROM encouragement')->fetchColumn();
$solde       = $totalFin - $utilise;

// Taux d'utilisation. La barre est plafonnée à 100 %, mais le taux affiché reste exact.
$tauxUtilisation = $totalFin > 0 ? round(($utilise / $totalFin) * 100, 1) : 0;
$largeurUtilisation = max(0, min(100, $tauxUtilisation));

// Répartition des dons par type.
$parType = array();
foreach (types_don() as $t) {
    $parType[$t] = array('n' => 0, 'somme' => 0.0);
}
foreach ($pdo->query('SELECT type_don, COUNT(*) AS n, COALESCE(SUM(montant),0) AS somme FROM Dons GROUP BY type_don') as $r) {
    if (isset($parType[$r['type_don']])) {
        $parType[$r['type_don']] = array('n' => (int)$r['n'], 'somme' => (float)$r['somme']);
    }
}
$maxN = max(1, max(array_map(function ($v) { return $v['n']; }, $parType)));

// Dons financiers des six derniers mois, y compris les mois sans don.
$mois = array();
$noms = array('Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc');
for ($i = 5; $i >= 0; $i--) {
    $ts = strtotime(date('Y-m-01') . " -$i month");
    $mois[date('Y-m', $ts)] = array('label' => $noms[(int)date('n', $ts) - 1], 'somme' => 0.0);
}
$debut = date('Y-m-01', strtotime(date('Y-m-01') . ' -5 month'));
$st = $pdo->prepare("SELECT DATE_FORMAT(date_don, '%Y-%m') AS m, SUM(montant) AS s FROM Dons WHERE type_don = 'Financier' AND date_don >= ? GROUP BY m");
$st->execute(array($debut));
foreach ($st->fetchAll() as $r) {
    if (isset($mois[$r['m']])) {
        $mois[$r['m']]['somme'] = (float)$r['s'];
    }
}
$maxM = 1;
foreach ($mois as $m) {
    $maxM = max($maxM, $m['somme']);
}

$derniers = $pdo->query('SELECT d.*, dn.nom, dn.prenom FROM Dons d JOIN Donateur dn ON dn.id = d.id_donateur ORDER BY d.date_don DESC, d.id_don DESC LIMIT 8')->fetchAll();
$urgents  = $pdo->query("SELECT * FROM Besoins WHERE priorite = 'haute' ORDER BY date_requete DESC LIMIT 5")->fetchAll();

$adminTitle = 'Tableau de bord';
$adminSub   = 'Vue d\'ensemble des dons, des besoins et de leur utilisation.';
$adminNav   = 'dash';
require __DIR__ . '/includes/admin_header.php';
?>

<section class="kpis" aria-label="Indicateurs principaux">
  <article class="kpi" data-reveal>
    <span class="ico"><?= svg_icon('coins') ?></span>
    <div class="v" data-count="<?= (int)$totalFin ?>" data-suffix=" F">0</div>
    <div class="l">Dons financiers reçus</div>
  </article>
  <article class="kpi leaf <?= $solde < 0 ? 'negative' : '' ?>" data-reveal style="--d:.06s">
    <span class="ico"><?= svg_icon('bank') ?></span>
    <div class="v" data-count="<?= (int)$solde ?>" data-suffix=" F">0</div>
    <div class="l">Solde disponible</div>
  </article>
  <article class="kpi sun" data-reveal style="--d:.12s">
    <span class="ico"><?= svg_icon('receipt') ?></span>
    <div class="v" data-count="<?= (int)$utilise ?>" data-suffix=" F">0</div>
    <div class="l">Montant déjà utilisé</div>
  </article>
  <article class="kpi" data-reveal style="--d:.18s">
    <span class="ico"><?= svg_icon('gift') ?></span>
    <div class="v" data-count="<?= $nbDons ?>">0</div>
    <div class="l">Dons enregistrés</div>
  </article>
  <article class="kpi" data-reveal style="--d:.24s">
    <span class="ico"><?= svg_icon('users') ?></span>
    <div class="v" data-count="<?= $nbDonateurs ?>">0</div>
    <div class="l">Donateurs</div>
  </article>
  <article class="kpi rose" data-reveal style="--d:.30s">
    <span class="ico"><?= svg_icon('clipboard') ?></span>
    <div class="v" data-count="<?= $nbBesoins ?>">0</div>
    <div class="l">Besoins publiés</div>
  </article>
  <article class="kpi" data-reveal style="--d:.36s">
    <span class="ico"><?= svg_icon('message') ?></span>
    <div class="v" data-count="<?= $nbMessages ?>">0</div>
    <div class="l">Messages reçus</div>
  </article>
</section>

<section class="panel" data-reveal aria-labelledby="usage-title">
  <h2 id="usage-title">Utilisation des dons financiers</h2>
  <div class="usage-summary">
    <strong><?= e(fcfa($utilise)) ?> utilisés sur <?= e(fcfa($totalFin)) ?></strong>
    <span><?= e((string)$tauxUtilisation) ?> % utilisés</span>
  </div>
  <div class="usage-track <?= $tauxUtilisation > 100 ? 'over' : '' ?>" role="progressbar" aria-label="Part des dons financiers utilisée" aria-valuemin="0" aria-valuemax="100" aria-valuenow="<?= (int)round($largeurUtilisation) ?>" style="--usage:<?= e((string)$largeurUtilisation) ?>%">
    <span></span>
  </div>
  <p class="usage-note">
    <?php if ($totalFin <= 0): ?>Aucun don financier n'a encore été enregistré.<?php elseif ($tauxUtilisation > 100): ?>Le montant réparti dépasse le total des dons financiers reçus. Vérifiez les répartitions.<?php else: ?>Solde restant : <?= e(fcfa($solde)) ?>.<?php endif; ?>
  </p>
</section>

<div class="cols">
  <div>
    <section class="panel" data-reveal>
      <h2>Dons financiers, 6 derniers mois</h2>
      <div class="months" role="img" aria-label="Graphique des dons financiers par mois sur les six derniers mois">
        <?php foreach ($mois as $m): ?>
          <?php $hauteur = $m['somme'] > 0 ? max(6, round($m['somme'] / $maxM * 100)) : 4; ?>
          <div class="m" title="<?= e($m['label'] . ' : ' . fcfa($m['somme'])) ?>">
            <b><?= $m['somme'] > 0 ? e(nombre($m['somme'])) : '0' ?></b>
            <div class="col" style="--h:<?= (int)$hauteur ?>%" aria-hidden="true"></div>
            <span class="month-label"><?= e($m['label']) ?></span>
          </div>
        <?php endforeach; ?>
      </div>
    </section>

    <section class="panel" data-reveal>
      <h2>Dons par type</h2>
      <?php foreach ($parType as $t => $v): ?>
        <?php $part = round($v['n'] / $maxN * 100); ?>
        <div class="hbar">
          <span><?= type_icon($t) ?> <?= e(type_label($t)) ?></span>
          <div class="bar" role="progressbar" aria-label="Nombre de dons <?= e(type_label($t)) ?>" aria-valuemin="0" aria-valuemax="<?= $maxN ?>" aria-valuenow="<?= $v['n'] ?>" title="<?= $v['n'] ?> don(s)">
            <span style="--w:<?= $part ?>%"></span>
          </div>
          <strong><?= $v['n'] ?><?= $t === 'Financier' ? ' · ' . e(fcfa($v['somme'])) : '' ?></strong>
        </div>
      <?php endforeach; ?>
    </section>
  </div>

  <div>
    <section class="panel" data-reveal>
      <h2><?= svg_icon('alert') ?> Besoins prioritaires</h2>
      <?php if ($urgents): foreach ($urgents as $b): ?>
        <div class="message-item">
          <div class="meta"><?= type_icon($b['type_besoin']) ?> <?= e($b['type_besoin']) ?> · <?= e(date_fr($b['date_requete'])) ?> · <?= e(fcfa($b['montant_estimatif'])) ?></div>
          <p><?= e(str_cut($b['description'], 110)) ?></p>
        </div>
      <?php endforeach; else: ?>
        <p class="muted">Aucun besoin de priorité haute.</p>
      <?php endif; ?>
      <p style="margin:16px 0 0">
        <a class="btn btn-sm" href="add_besoin.php"><?= svg_icon('plus') ?> Ajouter un besoin</a>
        <a class="btn btn-ghost btn-sm" href="manage_besoins.php">Tout gérer</a>
      </p>
    </section>

    <section class="panel" data-reveal>
      <h2><?= svg_icon('message') ?> Messages</h2>
      <p><strong><?= $nbMessages ?></strong> message<?= $nbMessages > 1 ? 's' : '' ?> d'encouragement reçu<?= $nbMessages > 1 ? 's' : '' ?>.</p>
      <a class="btn btn-ghost btn-sm" href="admin_messages.php">Lire les messages</a>
    </section>
  </div>
</div>

<section class="panel" data-reveal>
  <h2>Derniers dons</h2>
  <div class="table-wrap" role="region" aria-label="Liste des huit derniers dons" tabindex="0">
    <table class="table">
      <thead><tr><th>Date</th><th>Donateur</th><th>Type</th><th class="num">Montant</th><th>Détail</th></tr></thead>
      <tbody>
      <?php if ($derniers): foreach ($derniers as $d): ?>
        <tr>
          <td><?= e(date_fr($d['date_don'])) ?></td>
          <td><?= e($d['prenom'] . ' ' . $d['nom']) ?></td>
          <td><span class="badge b-type"><?= type_icon($d['type_don']) ?> <?= e(type_label($d['type_don'])) ?></span></td>
          <td class="num"><?= e(montant_don($d)) ?></td>
          <td><?= e(str_cut((string)$d['detail_don'], 60)) ?></td>
        </tr>
      <?php endforeach; else: ?>
        <tr><td colspan="5" class="empty">Aucun don pour l'instant.</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
  <p style="margin:16px 0 0"><a href="admin_dons.php">Voir tous les dons <?= svg_icon('arrow-right') ?></a></p>
</section>

<?php require __DIR__ . '/includes/admin_footer.php'; ?>
