<?php
require_once __DIR__ . '/includes/functions.php';
require_admin();

$besoins = $pdo->query("SELECT * FROM Besoins ORDER BY FIELD(priorite,'haute','moyenne','basse'), date_requete DESC")->fetchAll();

$adminTitle = 'Gestion des besoins';
$adminSub   = count($besoins) . ' besoin(s) publié(s) sur le site.';
$adminNav   = 'besoins';
require __DIR__ . '/includes/admin_header.php';
?>
<p><a class="btn" href="add_besoin.php"><?= svg_icon('plus') ?> Ajouter un besoin</a></p>
<div class="table-wrap"><table class="table">
  <thead><tr><th>Type</th><th>Description</th><th>Priorité</th><th>Date</th><th class="num">Montant estimatif</th><th></th></tr></thead>
  <tbody>
  <?php if ($besoins): foreach ($besoins as $b): ?>
    <tr>
      <td><?= type_icon($b['type_besoin']) ?> <?= e($b['type_besoin']) ?></td>
      <td><?= e(str_cut($b['description'], 90)) ?></td>
      <td><span class="badge b-<?= e($b['priorite']) ?>"><?= e(ucfirst($b['priorite'])) ?></span></td>
      <td><?= e(date_fr($b['date_requete'])) ?></td>
      <td class="num"><?= e(fcfa($b['montant_estimatif'])) ?></td>
      <td class="act"><div class="act-btns">
        <a class="btn btn-ghost btn-sm" href="edit_besoin.php?id=<?= (int)$b['id_besoin'] ?>"><?= svg_icon('edit') ?> Modifier</a>
        <a class="btn btn-danger btn-sm" href="delete_besoin.php?id=<?= (int)$b['id_besoin'] ?>" aria-label="Supprimer le besoin #<?= (int)$b['id_besoin'] ?>" title="Supprimer"><?= svg_icon('trash') ?></a>
      </div></td>
    </tr>
  <?php endforeach; else: ?><tr><td colspan="6" class="empty">Aucun besoin. Commence par en ajouter un.</td></tr><?php endif; ?>
  </tbody></table></div>
<?php require __DIR__ . '/includes/admin_footer.php'; ?>
