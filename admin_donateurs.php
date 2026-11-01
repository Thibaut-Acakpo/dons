<?php
require_once __DIR__ . '/includes/functions.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && post('action') === 'supprimer') {
  csrf_verify();
  $id = (int)post('id_donateur', 0);
  $st = $pdo->prepare('SELECT COUNT(*) FROM Dons WHERE id_donateur = ?');
  $st->execute(array($id));
  if ($id <= 0 || (int)$st->fetchColumn() > 0) {
    flash('err', 'Impossible de supprimer ce donateur : supprime d’abord ses dons.');
  } else {
    $st = $pdo->prepare('DELETE FROM Donateur WHERE id = ?');
    $st->execute(array($id));
    flash('ok', 'Donateur supprimé.');
  }
  redirect('admin_donateurs.php?' . http_build_query($_GET));
}

$q = get('q');
$where = $q !== '' ? 'WHERE dn.nom LIKE ? OR dn.prenom LIKE ? OR dn.numero LIKE ?' : '';
$params = $q !== '' ? array("%$q%", "%$q%", "%$q%") : array();
$st = $pdo->prepare("SELECT dn.*, COUNT(d.id_don) AS nb, COALESCE(SUM(d.montant),0) AS total, MAX(d.date_don) AS dernier
                     FROM Donateur dn LEFT JOIN Dons d ON d.id_donateur = dn.id $where GROUP BY dn.id ORDER BY dernier DESC, dn.id DESC");
$st->execute($params);
$rows = $st->fetchAll();

$adminTitle = 'Donateurs'; $adminSub = count($rows) . ' donateur(s).'; $adminNav = 'donateurs';
require __DIR__ . '/includes/admin_header.php';
?>
<form class="toolbar" method="get">
  <div class="field grow"><label for="q">Recherche</label><input class="input" id="q" name="q" placeholder="Nom, prénom ou téléphone" value="<?= e($q) ?>"></div>
  <button class="btn" type="submit">Rechercher</button>
</form>
<div class="table-wrap"><table class="table">
  <thead><tr><th>Donateur</th><th>Sexe</th><th>Téléphone</th><th class="num">Dons</th><th class="num">Total financier</th><th>Dernier don</th><th></th></tr></thead>
  <tbody>
  <?php if ($rows): foreach ($rows as $r): ?>
    <tr><td><?= e($r['prenom'] . ' ' . $r['nom']) ?></td><td><?= e($r['sexe']) ?></td><td><?= e($r['numero']) ?></td>
      <td class="num"><?= (int)$r['nb'] ?></td><td class="num"><?= e(fcfa($r['total'])) ?></td><td><?= e(date_fr($r['dernier'])) ?></td>
      <td class="act"><form method="post" style="display:inline" data-confirm="Supprimer définitivement ce donateur ?" data-confirm-title="Suppression du donateur" data-confirm-label="Supprimer" data-confirm-danger><?= csrf_field() ?><input type="hidden" name="action" value="supprimer"><input type="hidden" name="id_donateur" value="<?= (int)$r['id'] ?>"><button class="btn btn-danger btn-sm" type="submit" aria-label="Supprimer le donateur <?= e($r['prenom'] . ' ' . $r['nom']) ?>" title="Supprimer"><?= svg_icon('trash') ?></button></form></td></tr>
  <?php endforeach; else: ?><tr><td colspan="7" class="empty">Aucun donateur.</td></tr><?php endif; ?>
  </tbody></table></div>
<?php require __DIR__ . '/includes/admin_footer.php'; ?>
