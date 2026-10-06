<?php
require_once __DIR__ . '/includes/functions.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && post('action') === 'supprimer') {
    csrf_verify();
    $pdo->prepare('DELETE FROM encouragement WHERE id_encouragement = ?')->execute(array((int)post('id', 0)));
    flash('ok', 'Message supprimé.');
    redirect('admin_messages.php');
}
$msgs = $pdo->query('SELECT e.*, dn.nom, dn.prenom, dn.numero FROM encouragement e LEFT JOIN Donateur dn ON dn.id = e.id_donateur ORDER BY e.date_envoi DESC, e.id_encouragement DESC')->fetchAll();

$adminTitle = 'Messages reçus'; $adminSub = count($msgs) . ' message(s) depuis la page Contact.'; $adminNav = 'messages';
require __DIR__ . '/includes/admin_header.php';
?>
<section class="panel">
  <?php if ($msgs): foreach ($msgs as $m): ?>
    <div class="message-item">
      <div class="meta"><?= e(date('d/m/Y à H:i', strtotime($m['date_envoi']))) ?> ·
        <?= $m['prenom'] !== null ? e($m['prenom'] . ' ' . $m['nom'] . ' (' . $m['numero'] . ')') : 'Visiteur non identifié' ?></div>
      <p><?= e($m['message']) ?></p>
      <form method="post" style="margin-top:8px" data-confirm="Supprimer définitivement ce message ?" data-confirm-title="Suppression du message" data-confirm-label="Supprimer" data-confirm-danger><?= csrf_field() ?><input type="hidden" name="action" value="supprimer"><input type="hidden" name="id" value="<?= (int)$m['id_encouragement'] ?>">
        <button class="btn btn-ghost btn-sm" type="submit"><?= svg_icon('trash') ?> Supprimer</button></form>
    </div>
  <?php endforeach; else: ?><p class="muted">Aucun message pour l'instant.</p><?php endif; ?>
</section>
<?php require __DIR__ . '/includes/admin_footer.php'; ?>
