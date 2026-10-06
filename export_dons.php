<?php
require_once __DIR__ . '/includes/functions.php';
require_admin();

list($where, $params) = dons_filtres();
$st = $pdo->prepare('SELECT d.id_don, d.date_don, dn.nom, dn.prenom, dn.numero, d.type_don, d.montant, d.detail_don
                     FROM Dons d JOIN Donateur dn ON dn.id = d.id_donateur WHERE ' . $where . ' ORDER BY d.date_don DESC, d.id_don DESC');
$st->execute($params);

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="dons-' . date('Y-m-d') . '.csv"');
$out = fopen('php://output', 'w');
fwrite($out, "\xEF\xBB\xBF");                       // BOM : Excel lit correctement les accents
$neutre = function ($s) { return (is_string($s) && preg_match('/^[=+\-@\t\r]/', $s)) ? "'" . $s : $s; };   // anti-injection de formules
fputcsv($out, array('N°', 'Date', 'Nom', 'Prénom', 'Téléphone', 'Type', 'Montant (FCFA)', 'Détail'), ';');
while ($r = $st->fetch()) {
    fputcsv($out, array($r['id_don'], $r['date_don'], $neutre($r['nom']), $neutre($r['prenom']), $r['numero'], type_label($r['type_don']),
        $r['type_don'] === 'Financier' ? $r['montant'] : '', $neutre((string)$r['detail_don'])), ';');
}
fclose($out);
