<?php
require_once __DIR__ . '/includes/functions.php';
require_admin();

list($where, $params) = dons_filtres();
$base = 'FROM Dons d JOIN Donateur dn ON dn.id = d.id_donateur WHERE ' . $where;

// Suppression d'un don (POST + CSRF) — refusée si une répartition existe
if ($_SERVER['REQUEST_METHOD'] === 'POST' && post('action') === 'supprimer') {
    csrf_verify();
    $id = (int)post('id_don', 0);
    $n = $pdo->prepare('SELECT COUNT(*) FROM repartition WHERE id_don = ?'); $n->execute(array($id));
    if ((int)$n->fetchColumn() > 0) {
        flash('err', 'Ce don a déjà été réparti : supprime d\'abord ses répartitions.');
    } else {
        $pdo->prepare('DELETE FROM Dons WHERE id_don = ?')->execute(array($id));
        flash('ok', 'Don supprimé.');
    }
    redirect('admin_dons.php?' . http_build_query($_GET));
}

$tot = $pdo->prepare('SELECT COUNT(*) ' . $base); $tot->execute($params);
$pg = paginate((int)$tot->fetchColumn(), 15, (int)get('p', 1));
$st = $pdo->prepare('SELECT d.*, dn.nom, dn.prenom, dn.numero, (SELECT COUNT(*) FROM repartition r WHERE r.id_don = d.id_don) AS nb_rep ' . $base
                    . ' ORDER BY d.date_don DESC, d.id_don DESC LIMIT ' . (int)$pg['per'] . ' OFFSET ' . (int)$pg['offset']);
$st->execute($params);
$dons = $st->fetchAll();

$adminTitle = 'Tous les dons'; $adminSub = $pg['total'] . ' don(s) correspondant aux filtres.'; $adminNav = 'dons';
require __DIR__ . '/includes/admin_header.php';
?>
<form class="toolbar" method="get">
  <div class="field grow"><label for="q">Recherche</label><input class="input" id="q" name="q" placeholder="Nom, téléphone, détail…" value="<?= e(get('q')) ?>"></div>
  <div class="field"><label for="type">Type</label><select class="input" id="type" name="type"><option value="">Tous</option>
    <?php foreach (types_don() as $t): ?><option value="<?= e($t) ?>" <?= get('type') === $t ? 'selected' : '' ?>><?= e(type_label($t)) ?></option><?php endforeach; ?></select></div>
  <div class="field"><label for="du">Du</label><input class="input" id="du" name="du" type="date" value="<?= e(get('du')) ?>"></div>
  <div class="field"><label for="au">Au</label><input class="input" id="au" name="au" type="date" value="<?= e(get('au')) ?>"></div>
  <button class="btn" type="submit">Filtrer</button>
  <a class="btn btn-ghost" href="admin_dons.php">Réinitialiser</a>
  <a class="btn btn-sun" href="export_dons.php?<?= e(http_build_query(array_filter(array('q' => get('q'), 'type' => get('type'), 'du' => get('du'), 'au' => get('au'))))) ?>"><?= svg_icon('download') ?> Exporter CSV</a>
  <a class="btn btn-ghost" href="export_dons_pdf.php?<?= e(http_build_query(array_filter(array('q' => get('q'), 'type' => get('type'), 'du' => get('du'), 'au' => get('au'))))) ?>"><?= svg_icon('pdf') ?> Télécharger PDF</a>
</form>
<div class="table-wrap"><table class="table">
  <thead><tr><th>#</th><th>Date</th><th>Donateur</th><th>Type</th><th class="num">Montant</th><th>Détail</th><th></th></tr></thead>
  <tbody>
  <?php if ($dons): foreach ($dons as $d): ?>
    <tr>
      <td>#<?= (int)$d['id_don'] ?></td><td><?= e(date_fr($d['date_don'])) ?></td>
      <td><?= e($d['prenom'] . ' ' . $d['nom']) ?><br><small class="muted"><?= e($d['numero']) ?></small></td>
      <td><span class="badge b-type"><?= type_icon($d['type_don']) ?> <?= e(type_label($d['type_don'])) ?></span></td>
      <td class="num"><?= e(montant_don($d)) ?></td><td><?= e(str_cut((string)$d['detail_don'], 70)) ?></td>
      <td class="act"><div class="act-btns">
        <a class="btn btn-ghost btn-sm" href="add_repartition.php?don=<?= (int)$d['id_don'] ?>"><?= svg_icon('plus') ?> Répartir</a>
        <a class="btn btn-ghost btn-sm" href="recu.php?id=<?= (int)$d['id_don'] ?>" target="_blank" rel="noopener" aria-label="Ouvrir le reçu du don #<?= (int)$d['id_don'] ?>" title="Ouvrir le reçu"><?= svg_icon('receipt') ?></a>
        <form method="post" style="display:inline" data-confirm="Supprimer définitivement ce don ?" data-confirm-title="Suppression du don" data-confirm-label="Supprimer" data-confirm-danger><?= csrf_field() ?><input type="hidden" name="action" value="supprimer"><input type="hidden" name="id_don" value="<?= (int)$d['id_don'] ?>">
          <button class="btn btn-danger btn-sm" type="submit" aria-label="Supprimer le don #<?= (int)$d['id_don'] ?>" title="Supprimer"><?= svg_icon('trash') ?></button></form>
      </div></td>
    </tr>
  <?php endforeach; else: ?><tr><td colspan="7" class="empty">Aucun don trouvé.</td></tr><?php endif; ?>
  </tbody></table></div>
<?= pager_html($pg, $_GET) ?>
<?php require __DIR__ . '/includes/admin_footer.php'; ?>
