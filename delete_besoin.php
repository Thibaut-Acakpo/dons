<?php
require_once __DIR__ . '/includes/functions.php';
require_admin();

$id = (int)(isset($_POST['id']) ? $_POST['id'] : get('id', 0));
$st = $pdo->prepare('SELECT * FROM Besoins WHERE id_besoin = ?');
$st->execute(array($id));
$b = $st->fetch();
if (!$b) { flash('err', 'Besoin introuvable.'); redirect('manage_besoins.php'); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $pdo->prepare('DELETE FROM Besoins WHERE id_besoin = ?')->execute(array($id));
    flash('ok', 'Besoin supprimé.');
    redirect('manage_besoins.php');
}
$adminTitle = 'Supprimer un besoin'; $adminNav = 'besoins';
require __DIR__ . '/includes/admin_header.php';
?>
<form class="panel confirm-box" method="post">
  <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$id ?>">
  <h2>Supprimer ce besoin ?</h2>
  <dl><dt>Type</dt><dd><?= e($b['type_besoin']) ?></dd><dt>Description</dt><dd><?= e($b['description']) ?></dd>
    <dt>Priorité</dt><dd><?= e(ucfirst($b['priorite'])) ?></dd><dt>Date</dt><dd><?= e(date_fr($b['date_requete'])) ?></dd></dl>
  <p class="muted">Cette action est définitive.</p>
  <button class="btn btn-danger" type="submit">Oui, supprimer</button> <a class="btn btn-ghost" href="manage_besoins.php">Annuler</a>
</form>
<?php require __DIR__ . '/includes/admin_footer.php'; ?>
