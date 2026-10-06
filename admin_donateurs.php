<?php
require_once __DIR__ . '/includes/functions.php';
require_admin();

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
  <thead><tr><th>Donateur</th><th>Sexe</th><th>Téléphone</th><th class="num">Dons</th><th class="num">Total financier</th><th>Dernier don</th></tr></thead>
  <tbody>
  <?php if ($rows): foreach ($rows as $r): ?>
    <tr><td><?= e($r['prenom'] . ' ' . $r['nom']) ?></td><td><?= e($r['sexe']) ?></td><td><?= e($r['numero']) ?></td>
      <td class="num"><?= (int)$r['nb'] ?></td><td class="num"><?= e(fcfa($r['total'])) ?></td><td><?= e(date_fr($r['dernier'])) ?></td></tr>
  <?php endforeach; else: ?><tr><td colspan="6" class="empty">Aucun donateur.</td></tr><?php endif; ?>
  </tbody></table></div>
<?php require __DIR__ . '/includes/admin_footer.php'; ?>
