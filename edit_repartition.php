<?php
require_once __DIR__ . '/includes/functions.php';
require_admin();

$id = (int)get('id_repartition', (int)get('id', 0));
$st = $pdo->prepare('SELECT * FROM repartition WHERE id_repartition = ?');
$st->execute(array($id));
$r = $st->fetch();
if (!$r) { flash('err', 'Répartition introuvable.'); redirect('view_repartition.php'); }

$errors = array();
$v = array('id_don' => (int)$r['id_don'], 'USA' => $r['USA'],
           'montant_utilise' => (float)$r['montant_utilise'] > 0 ? rtrim(rtrim($r['montant_utilise'], '0'), '.') : '',
           'date_utilisation' => $r['date_utilisation']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    list($errors, $data) = repartition_valider($id);
    $v = array('id_don' => (int)post('id_don', 0), 'USA' => post('USA'), 'montant_utilise' => post('montant_utilise'), 'date_utilisation' => post('date_utilisation'));
    if (!$errors) {
        $st = $pdo->prepare('UPDATE repartition SET id_don = ?, USA = ?, montant_utilise = ?, date_utilisation = ? WHERE id_repartition = ?');
        $st->execute(array($data['id_don'], $data['USA'], $data['montant_utilise'], $data['date_utilisation'], $id));
        flash('ok', 'Répartition mise à jour avec succès.');
        redirect('view_repartition.php');
    }
}
$dons = dons_avec_solde($id);
$adminTitle = 'Modifier la répartition'; $adminNav = 'repart'; $submitLabel = 'Mettre à jour'; $confirmEdit = true;
require __DIR__ . '/includes/admin_header.php';
require __DIR__ . '/includes/repartition_form.php';
require __DIR__ . '/includes/admin_footer.php';
