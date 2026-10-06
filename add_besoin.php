<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/besoin_valider.php';
require_admin();

$errors = array();
$v = array('type_besoin' => 'Alimentaire', 'description' => '', 'priorite' => 'moyenne', 'date_requete' => date('Y-m-d'), 'montant_estimatif' => '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    list($errors, $v, $sql) = besoin_valider();
    if (!$errors) {
        $st = $pdo->prepare('INSERT INTO Besoins (type_besoin, description, priorite, date_requete, montant_estimatif) VALUES (?, ?, ?, ?, ?)');
        $st->execute($sql);
        flash('ok', 'Le besoin a été ajouté avec succès.');
        redirect('manage_besoins.php');
    }
}
$adminTitle = 'Ajouter un besoin'; $adminNav = 'add_b'; $submitLabel = 'Ajouter le besoin';
require __DIR__ . '/includes/admin_header.php';
require __DIR__ . '/includes/besoin_form.php';
require __DIR__ . '/includes/admin_footer.php';
