<?php
require_once __DIR__ . '/includes/functions.php';
require_admin();

$id = (int)(isset($_POST['id']) ? $_POST['id'] : get('id', 0));
$st = $pdo->prepare('SELECT r.*, d.type_don, d.detail_don FROM repartition r JOIN Dons d ON d.id_don = r.id_don WHERE r.id_repartition = ?');
$st->execute(array($id));
$r = $st->fetch();
if (!$r) { flash('err', 'Répartition introuvable.'); redirect('view_repartition.php'); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $pdo->prepare('DELETE FROM repartition WHERE id_repartition = ?')->execute(array($id));
    flash('ok', 'Répartition supprimée.');
    redirect('view_repartition.php');
}
$adminTitle = 'Supprimer une répartition'; $adminNav = 'repart';
require __DIR__ . '/includes/admin_header.php';
?>
<form class="panel confirm-box" method="post">
  <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$id ?>">
  <h2>Supprimer cette répartition ?</h2>
  <dl><dt>Don</dt><dd>#<?= (int)$r['id_don'] ?> — <?= e(type_label($r['type_don'])) ?></dd><dt>Usage</dt><dd><?= e($r['USA']) ?></dd>
    <dt>Montant utilisé</dt><dd><?= e(fcfa($r['montant_utilise'])) ?></dd><dt>Date</dt><dd><?= e(date_fr($r['date_utilisation'])) ?></dd></dl>
  <p class="muted">Le solde du don sera recalculé. Cette action est définitive.</p>
  <button class="btn btn-danger" type="submit">Oui, supprimer</button> <a class="btn btn-ghost" href="view_repartition.php">Annuler</a>
</form>
<?php require __DIR__ . '/includes/admin_footer.php'; ?>
