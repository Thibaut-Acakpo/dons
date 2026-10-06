<?php
require_once __DIR__ . '/includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect('faire_un_don.php'); }
csrf_verify();

$numero = normalize_phone(post('numero'));
$action = post('action');

if (!valid_phone($numero) || !in_array($action, array('nouveau_don', 'consulter_dons'), true)) {
    flash('err', 'Renseigne un numéro de téléphone valide et choisis une action.');
    redirect('faire_un_don.php');
}
if (throttle_blocked('lookup', 12, 900)) {
    flash('err', 'Trop de tentatives. Réessaie dans quelques minutes.');
    redirect('faire_un_don.php');
}

$donateur = find_donateur_by_phone($numero);
if (!$donateur) {
    throttle_hit('lookup');
    flash('info', 'Aucun donateur enregistré avec ce numéro : inscris-toi en faisant ton premier don.');
    redirect('faire_don.php?numero=' . urlencode($numero));
}

$_SESSION['donateur_id'] = (int)$donateur['id'];
if ($action === 'nouveau_don') { redirect('faire_don.php'); }
redirect('merci.php');
