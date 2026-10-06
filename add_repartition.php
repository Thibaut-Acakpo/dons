<?php
require_once __DIR__ . '/includes/functions.php';
require_admin();

$errors = array();
$v = array('id_don' => (int)get('don', 0), 'USA' => '', 'montant_utilise' => '', 'date_utilisation' => date('Y-m-d'));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    list($errors, $data) = repartition_valider(0);
    $v = array('id_don' => (int)post('id_don', 0), 'USA' => post('USA'), 'montant_utilise' => post('montant_utilise'), 'date_utilisation' => post('date_utilisation'));
    if (!$errors) {
        $st = $pdo->prepare('INSERT INTO repartition (id_don, USA, montant_utilise, date_utilisation) VALUES (?, ?, ?, ?)');
        $st->execute(array($data['id_don'], $data['USA'], $data['montant_utilise'], $data['date_utilisation']));
        flash('ok', 'Répartition ajoutée avec succès.');
        redirect('view_repartition.php');
    }
}
$dons = dons_avec_solde(0);
$adminTitle = 'Ajouter une répartition'; $adminSub = 'Indique à quoi a servi un don.'; $adminNav = 'add_r'; $submitLabel = 'Ajouter';
require __DIR__ . '/includes/admin_header.php';
require __DIR__ . '/includes/repartition_form.php';
require __DIR__ . '/includes/admin_footer.php';
