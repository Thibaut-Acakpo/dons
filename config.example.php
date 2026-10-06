<?php
/** Copie ce fichier vers config.php puis renseigne tes paramètres locaux. */

define('DB_HOST', 'localhost');
define('DB_NAME', 'gestion_dons');
define('DB_USER', 'root');
define('DB_PASS', '');

define('SITE_NAME', 'Orphelinat du Centre YƐtƐn');
define('SITE_SHORT', 'Centre YƐtƐn');
define('SITE_EMAIL', 'contact@example.com');
define('SITE_PHONE', '+229 01 00 00 00 00');
define('SITE_PAYMENT_INFO', '');

define('ADMIN_EMAIL', 'admin@example.com');
define('ADMIN_PASSWORD_HASH', '');

define('APP_DEBUG', false);

date_default_timezone_set('Africa/Porto-Novo');

function fatal_page($titre, $detail = '', $code = 500) {
    if (!headers_sent()) {
        http_response_code($code);
        header('Content-Type: text/html; charset=utf-8');
    }
    echo '<!DOCTYPE html><html lang="fr"><meta charset="utf-8"><title>Erreur</title><body>';
    echo '<h1>' . htmlspecialchars($titre, ENT_QUOTES, 'UTF-8') . '</h1>';
    echo '<p>Vérifie WAMP, les paramètres de config.php et l’import de la base.</p>';
    if (APP_DEBUG && $detail !== '') {
        echo '<pre>' . htmlspecialchars($detail, ENT_QUOTES, 'UTF-8') . '</pre>';
    }
    echo '</body></html>';
    exit;
}

// Connexion PDO : conserve ce bloc identique dans config.php.
try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        array(
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        )
    );
} catch (PDOException $e) {
    fatal_page('Connexion à la base impossible', $e->getMessage());
}

set_exception_handler(function ($e) {
    fatal_page('Une erreur est survenue', get_class($e) . ' : ' . $e->getMessage());
});