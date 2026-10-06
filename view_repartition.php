<?php
require_once __DIR__ . '/includes/functions.php';
require_admin();

$rows = $pdo->query('SELECT r.*, d.type_don, d.detail_don, d.montant, d.date_don, dn.nom, dn.prenom
                     FROM repartition r JOIN Dons d ON d.id_don = r.id_don JOIN Donateur dn ON dn.id = d.id_donateur
                     ORDER BY r.date_utilisation DESC, r.id_repartition DESC')->fetchAll();

$adminTitle = 'Répartitions des dons'; $adminSub = 'À quoi ont servi les dons reçus.'; $adminNav = 'repart';
require __DIR__ . '/includes/admin_header.php';
?>
<p><a class="btn" href="add_repartition.php"><?= svg_icon('plus') ?> Ajouter une répartition</a></p>
<div class="table-wrap"><table class="table">
  <thead><tr><th>Date</th><th>Don</th><th>Donateur</th><th>Usage</th><th class="num">Utilisé</th><th class="num">Reste du don</th><th></th></tr></thead>
  <tbody>
  <?php if ($rows):
    $reste = array();
    foreach ($pdo->query('SELECT id_don, SUM(montant_utilise) s FROM repartition GROUP BY id_don') as $x) { $reste[$x['id_don']] = (float)$x['s']; }
    foreach ($rows as $r): $fin = $r['type_don'] === 'Financier'; ?>
    <tr>
      <td><?= e(date_fr($r['date_utilisation'])) ?></td>
      <td><span class="badge b-type"><?= type_icon($r['type_don']) ?> #<?= (int)$r['id_don'] ?> <?= e(type_label($r['type_don'])) ?></span><br><small class="muted"><?= e($fin ? fcfa($r['montant']) : str_cut((string)$r['detail_don'], 40)) ?></small></td>
      <td><?= e($r['prenom'] . ' ' . $r['nom']) ?></td>
      <td><?= e($r['USA']) ?></td>
      <td class="num"><?= $fin ? e(fcfa($r['montant_utilise'])) : '—' ?></td>
      <td class="num"><?= $fin ? e(fcfa(max(0, (float)$r['montant'] - (isset($reste[$r['id_don']]) ? $reste[$r['id_don']] : 0)))) : '—' ?></td>
      <td class="act"><div class="act-btns">
        <a class="btn btn-ghost btn-sm" href="edit_repartition.php?id_repartition=<?= (int)$r['id_repartition'] ?>" aria-label="Modifier la répartition #<?= (int)$r['id_repartition'] ?>" title="Modifier"><?= svg_icon('edit') ?></a>
        <a class="btn btn-danger btn-sm" href="delete_repartition.php?id=<?= (int)$r['id_repartition'] ?>" aria-label="Supprimer la répartition #<?= (int)$r['id_repartition'] ?>" title="Supprimer"><?= svg_icon('trash') ?></a>
      </div></td>
    </tr>
  <?php endforeach; else: ?><tr><td colspan="7" class="empty">Aucune répartition enregistrée.</td></tr><?php endif; ?>
  </tbody></table></div>
<?php require __DIR__ . '/includes/admin_footer.php'; ?>
