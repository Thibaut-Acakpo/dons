<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/besoin_valider.php';
require_admin();

$id = (int)get('id', 0);
if (!$id) { redirect('manage_besoins.php'); }
$st = $pdo->prepare('SELECT * FROM Besoins WHERE id_besoin = ?');
$st->execute(array($id));
$b = $st->fetch();
if (!$b) { flash('err', 'Besoin introuvable.'); redirect('manage_besoins.php'); }

$errors = array();
$v = array('type_besoin' => $b['type_besoin'], 'description' => $b['description'], 'priorite' => $b['priorite'],
           'date_requete' => $b['date_requete'], 'montant_estimatif' => $b['montant_estimatif'] === null ? '' : rtrim(rtrim($b['montant_estimatif'], '0'), '.'));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    list($errors, $v, $sql) = besoin_valider();
    if (!$errors) {
        $sql[] = $id;
        $st = $pdo->prepare('UPDATE Besoins SET type_besoin = ?, description = ?, priorite = ?, date_requete = ?, montant_estimatif = ? WHERE id_besoin = ?');
        $st->execute($sql);
        flash('ok', 'Besoin mis à jour avec succès.');
        redirect('manage_besoins.php');
    }
}
$adminTitle = 'Modifier un besoin'; $adminNav = 'besoins'; $submitLabel = 'Enregistrer les modifications'; $confirmEdit = true;
require __DIR__ . '/includes/admin_header.php';
require __DIR__ . '/includes/besoin_form.php';
require __DIR__ . '/includes/admin_footer.php';
