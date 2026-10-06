<?php
// Utilitaire en ligne de commande uniquement :
//   php generer_hash.php "MonNouveauMotDePasse"
// Colle ensuite le résultat dans ADMIN_PASSWORD_HASH (config.php).
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
if ($argc < 2 || strlen($argv[1]) < 8) {
    fwrite(STDERR, "Usage : php generer_hash.php \"mot_de_passe_de_8_caracteres_minimum\"\n");
    exit(1);
}
echo password_hash($argv[1], PASSWORD_DEFAULT), PHP_EOL;
