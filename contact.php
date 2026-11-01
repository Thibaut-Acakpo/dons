<?php
require_once __DIR__ . '/includes/functions.php';

header('Content-Type: application/json');

$erreur = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $message = post('message');
    if (throttle_blocked('contact', 5, 600)) {
        $erreur = 'Tu as envoyé plusieurs messages à la suite. Réessaie dans quelques minutes.';
    } elseif (str_len($message) < 5) {
        $erreur = 'Ton message est trop court (5 caractères minimum).';
    } elseif (str_len($message) > 2000) {
        $erreur = 'Ton message est trop long (2000 caractères maximum).';
    } else {
        $idDonateur = !empty($_SESSION['donateur_id']) ? (int)$_SESSION['donateur_id'] : null;
        $st = $pdo->prepare("INSERT INTO encouragement (message, frequence, date_envoi, id_donateur) VALUES (?, 'unique', NOW(), ?)");
        $st->execute(array($message, $idDonateur));
        throttle_hit('contact');
        
        echo json_encode(['success' => true, 'message' => 'Votre message a bien été envoyé. Merci pour votre soutien !']);
        exit;
    }
}

echo json_encode(['success' => false, 'message' => $erreur]);
exit;
