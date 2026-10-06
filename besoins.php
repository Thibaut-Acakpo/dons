<?php
require_once __DIR__ . '/includes/functions.php';

$fType = in_array(get('type'), types_besoin(), true) ? get('type') : '';
$fPrio = in_array(get('priorite'), priorites(), true) ? get('priorite') : '';

$where = array(); $params = array();
if ($fType !== '') { $where[] = 'type_besoin = ?'; $params[] = $fType; }
if ($fPrio !== '') { $where[] = 'priorite = ?';    $params[] = $fPrio; }
$sql = 'SELECT * FROM Besoins' . ($where ? ' WHERE ' . implode(' AND ', $where) : '')
     . " ORDER BY FIELD(priorite,'haute','moyenne','basse'), date_requete DESC";
$st = $pdo->prepare($sql);
$st->execute($params);
$besoins = $st->fetchAll();

function lien_filtre($type, $prio) {
    $q = array();
    if ($type !== '') { $q['type'] = $type; }
    if ($prio !== '') { $q['priorite'] = $prio; }
    return 'besoins.php' . ($q ? '?' . http_build_query($q) : '');
}

$pageTitle = 'Nos besoins';
$nav = 'besoins';
require __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
  <span class="blob b1"></span><span class="blob b2"></span>
  <div class="wrap"><h1 class="rise">Nos besoins prioritaires</h1><p class="rise" style="--d:.12s">Aidez-nous à mieux soutenir nos enfants : choisissez un besoin et contribuez directement.</p></div>
</section>
<section class="section" style="padding-top:34px">
  <div class="wrap">
    <div class="chips" style="margin:0 0 8px" aria-label="Filtrer par type">
      <a class="chip <?= $fType === '' ? 'on' : '' ?>" href="<?= e(lien_filtre('', $fPrio)) ?>">Tous les types</a>
      <?php foreach (types_besoin() as $t): ?><a class="chip <?= $fType === $t ? 'on' : '' ?>" href="<?= e(lien_filtre($t, $fPrio)) ?>"><?= type_icon($t) ?> <?= e($t) ?></a><?php endforeach; ?>
    </div>
    <div class="chips" style="margin:0 0 30px" aria-label="Filtrer par priorité">
      <a class="chip <?= $fPrio === '' ? 'on' : '' ?>" href="<?= e(lien_filtre($fType, '')) ?>">Toutes priorités</a>
      <?php foreach (priorites() as $p): ?><a class="chip <?= $fPrio === $p ? 'on' : '' ?>" href="<?= e(lien_filtre($fType, $p)) ?>">Priorité <?= e($p) ?></a><?php endforeach; ?>
    </div>

    <?php if ($besoins): ?>
      <div class="grid g3">
        <?php foreach ($besoins as $i => $b): ?>
          <article class="card need tilt" data-reveal style="--d:<?= min($i, 5) * 0.07 ?>s">
            <div class="top"><span class="ico"><?= type_icon($b['type_besoin']) ?></span><span class="badge b-<?= e($b['priorite']) ?>">Priorité <?= e($b['priorite']) ?></span></div>
            <h3><?= e($b['type_besoin']) ?></h3>
            <p class="muted"><?= e($b['description']) ?></p>
            <p class="muted" style="font-size:.85rem">Demande du <?= e(date_fr($b['date_requete'])) ?></p>
            <div class="foot">
              <span class="amount"><?= $b['montant_estimatif'] !== null ? e(fcfa($b['montant_estimatif'])) : 'Montant libre' ?></span>
              <a class="btn btn-sm" href="faire_don.php?type=<?= e(besoin_vers_don($b['type_besoin'])) ?>"><?= svg_icon('heart') ?> Contribuer</a>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="card center"><p>Aucun besoin ne correspond à ce filtre pour l'instant.</p><a class="btn btn-ghost" href="besoins.php">Voir tous les besoins</a></div>
    <?php endif; ?>
    <p class="center" style="margin-top:36px"><a class="btn btn-ghost" href="index.php"><?= svg_icon('arrow-left') ?> Retour à l'accueil</a></p>
  </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
