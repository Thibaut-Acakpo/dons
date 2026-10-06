<?php
/**
 * Fonctions communes à tout le site.
 * Écrit pour rester compatible avec PHP 7.2 et plus.
 */

require_once __DIR__ . '/../config.php';

// ---------------------------------------------------------------- Session
if (session_status() === PHP_SESSION_NONE) {
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    if (PHP_VERSION_ID >= 70300) {
        session_set_cookie_params(array(
            'lifetime' => 0, 'path' => '/', 'secure' => $https,
            'httponly' => true, 'samesite' => 'Lax',
        ));
    } else {
        session_set_cookie_params(0, '/', '', $https, true);
    }
    session_name('gestiondon');
    session_start();
}

// ---------------------------------------------------------------- Constantes métier
function types_don()      { return array('Alimentaire', 'Vestimentaire', 'Medical', 'Financier'); }
function types_besoin()   { return array('Alimentaire', 'Vestimentaire', 'Médical', 'Financier'); }
function priorites()      { return array('haute', 'moyenne', 'basse'); }
function sexes()          { return array('Homme', 'Femme', 'Autre'); }

function type_label($t) { return $t === 'Medical' ? 'Médical' : (string)$t; }

function svg_icon($name) {
    $paths = array(
        'alert' => '<path d="M10.3 3.9 2.6 17.2A2 2 0 0 0 4.3 20h15.4a2 2 0 0 0 1.7-2.8L13.7 3.9a2 2 0 0 0-3.4 0Z"/><path d="M12 9v4m0 3h.01"/>',
        'arrow-left' => '<path d="m15 18-6-6 6-6M9 12h12"/>',
        'arrow-right' => '<path d="m9 18 6-6-6-6m6 6H3"/>',
        'bank' => '<path d="m3 9 9-6 9 6M4 10h16M5 10v8m4-8v8m6-8v8m4-8v8M3 18h18M2 21h20"/>',
        'clipboard' => '<rect x="5" y="4" width="14" height="17" rx="2"/><path d="M9 4V3h6v1m-6 5h6m-6 4h6m-6 4h4"/>',
        'close' => '<path d="m18 6-12 12M6 6l12 12"/>',
        'coins' => '<circle cx="8" cy="8" r="5"/><path d="M11 4.2a5 5 0 1 1-2.8 8.6M8 6v4m-2-2h4"/>',
        'download' => '<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4m4-5 5 5 5-5m-5 5V3"/>',
        'food' => '<path d="M3 12h18a8 8 0 0 1-8 8h-2a8 8 0 0 1-8-8Zm2-3 2-2m3 2 2-3m3 3 2-2"/>',
        'globe' => '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a15 15 0 0 1 0 18m0-18a15 15 0 0 0 0 18"/>',
        'gift' => '<path d="M20 12v9H4v-9m-2-5h20v5H2zm10 0v14m0-14H7.5a2.5 2.5 0 1 1 2.5-2.5L12 7Zm0 0h4.5A2.5 2.5 0 1 0 14 4.5L12 7Z"/>',
        'heart' => '<path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1.1-1.1a5.5 5.5 0 0 0-7.8 7.8l1.1 1.1L12 21l7.8-7.5 1.1-1.1a5.5 5.5 0 0 0-.1-7.8Z"/>',
        'home' => '<path d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1h-5v-7h-6v7H4a1 1 0 0 1-1-1Z"/>',
        'mail' => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>',
        'menu' => '<path d="M4 6h16M4 12h16M4 18h16"/>',
        'message' => '<path d="M21 11.5a8.4 8.4 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.4 8.4 0 0 1-3.8-.9L3 21l1.9-5.7a8.4 8.4 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.4 8.4 0 0 1 3.8-.9h.5a8.5 8.5 0 0 1 8 8Z"/>',
        'medical' => '<rect x="3" y="4" width="18" height="16" rx="2"/><path d="M12 8v8m-4-4h8"/>',
        'mobile' => '<rect x="6" y="2" width="12" height="20" rx="2"/><path d="M11 18h2"/>',
        'palette' => '<path d="M12 3a9 9 0 1 0 0 18h1a2 2 0 0 0 1.7-3.1 2 2 0 0 1 1.7-3.1H18a3 3 0 0 0 3-3c0-4.9-4-8.8-9-8.8Z"/><path d="M7.5 10h.01M10 7h.01m5 0h.01m2.5 4h.01"/>',
        'phone' => '<path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.4 19.4 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7l.5 3a2 2 0 0 1-.6 1.7L7.7 9.7a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 1.7-.6l3 .5a2 2 0 0 1 1.7 2.6Z"/>',
        'plus' => '<path d="M12 5v14m-7-7h14"/>',
        'printer' => '<path d="M6 9V3h12v6M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><path d="M6 14h12v7H6zm12-3h.01"/>',
        'pdf' => '<path d="M6 2h9l5 5v15H6z"/><path d="M14 2v6h6M8 14h8m-8 4h6"/>',
        'receipt' => '<path d="M5 3v18l3-2 4 2 4-2 3 2V3l-3 2-4-2-4 2-3-2Z"/><path d="M9 9h6m-6 4h6"/>',
        'share' => '<circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><path d="m8.7 10.7 6.6-4.4m-6.6 7 6.6 4.1"/>',
        'shirt' => '<path d="m16 3 5 3-3 5-3-1v11H9V10l-3 1-3-5 5-3a4 4 0 0 0 8 0Z"/>',
        'trash' => '<path d="M3 6h18m-2 0-.9 14H5.9L5 6m4 0V4h6v2m-5 4v6m4-6v6"/>',
        'unlock' => '<rect x="4" y="10" width="16" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 7.7-1.5M12 14v3"/>',
        'user' => '<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>',
        'users' => '<path d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2m6-10a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm6-7a4 4 0 0 1 0 8m2 4a4 4 0 0 1 4 4v1"/>',
        'edit' => '<path d="m16 4 4 4M4 20l4-.8L19 8a2.8 2.8 0 0 0-4-4L4 15l0 5Z"/>',
    );
    if (!isset($paths[$name])) { return ''; }
    return '<svg class="icon icon-' . $name . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $paths[$name] . '</svg>';
}

function type_icon($t) {
    $map = array('Alimentaire' => 'food', 'Vestimentaire' => 'shirt', 'Medical' => 'medical', 'Médical' => 'medical', 'Financier' => 'coins');
    return svg_icon(isset($map[$t]) ? $map[$t] : 'gift');
}

// Un besoin « Médical » correspond à un don « Medical » (orthographe historique de la base).
function besoin_vers_don($t) { return $t === 'Médical' ? 'Medical' : $t; }

// ---------------------------------------------------------------- Sécurité / affichage
function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }

function redirect($url) {
    header('Location: ' . $url);
    exit;
}

function str_len($s) { return function_exists('mb_strlen') ? mb_strlen($s, 'UTF-8') : strlen($s); }

function str_cut($s, $n) {
    if (str_len($s) <= $n) { return $s; }
    return (function_exists('mb_substr') ? mb_substr($s, 0, $n, 'UTF-8') : substr($s, 0, $n)) . '…';
}

function post($key, $default = '') {
    return isset($_POST[$key]) && !is_array($_POST[$key]) ? trim((string)$_POST[$key]) : $default;
}
function get($key, $default = '') {
    return isset($_GET[$key]) && !is_array($_GET[$key]) ? trim((string)$_GET[$key]) : $default;
}
function old($key, $default = '') {
    return e(isset($_POST[$key]) && !is_array($_POST[$key]) ? $_POST[$key] : $default);
}

function csrf_token() {
    if (empty($_SESSION['_csrf'])) { $_SESSION['_csrf'] = bin2hex(random_bytes(32)); }
    return $_SESSION['_csrf'];
}
function csrf_field() { return '<input type="hidden" name="_token" value="' . e(csrf_token()) . '">'; }
function csrf_verify() {
    $t = isset($_POST['_token']) ? (string)$_POST['_token'] : '';
    if ($t === '' || empty($_SESSION['_csrf']) || !hash_equals($_SESSION['_csrf'], $t)) {
        fatal_page('Session expirée', 'Le formulaire a expiré. Retourne en arrière, recharge la page puis réessaie.', 419);
    }
}

// Limiteur d'essais (par session) : $max tentatives par fenêtre de $window secondes
function throttle_blocked($key, $max, $window) {
    $now  = time();
    $hits = isset($_SESSION['_thr'][$key]) ? $_SESSION['_thr'][$key] : array();
    $keep = array();
    foreach ($hits as $t) { if ($t > $now - $window) { $keep[] = $t; } }
    $_SESSION['_thr'][$key] = $keep;
    return count($keep) >= $max;
}
function throttle_hit($key) { $_SESSION['_thr'][$key][] = time(); }

// ---------------------------------------------------------------- Messages flash
function flash($type, $message) { $_SESSION['_flash'][] = array($type, $message); }
function flash_render() {
    if (empty($_SESSION['_flash'])) { return; }
    foreach ($_SESSION['_flash'] as $f) {
        echo '<div class="wrap"><div class="alert alert-' . e($f[0]) . '" role="status">' . e($f[1]) . '</div></div>';
    }
    unset($_SESSION['_flash']);
}

// ---------------------------------------------------------------- Authentification admin
function is_admin() {
    if (empty($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) { return false; }
    if (isset($_SESSION['admin_last']) && time() - $_SESSION['admin_last'] > 7200) { // 2 h d'inactivité
        unset($_SESSION['admin_logged_in'], $_SESSION['admin_last']);
        return false;
    }
    $_SESSION['admin_last'] = time();
    return true;
}
function require_admin() {
    if (!is_admin()) { redirect('admin_login.php'); }
}

// ---------------------------------------------------------------- Donateur courant (session)
function current_donateur() {
    global $pdo;
    if (empty($_SESSION['donateur_id'])) { return null; }
    $st = $pdo->prepare('SELECT * FROM Donateur WHERE id = ?');
    $st->execute(array((int)$_SESSION['donateur_id']));
    $d = $st->fetch();
    return $d ? $d : null;
}

// ---------------------------------------------------------------- Téléphone
// Garde uniquement les chiffres et retire l'indicatif +229 (Bénin) s'il est présent.
function normalize_phone($s) {
    $d = preg_replace('/\D+/', '', (string)$s);
    if (strpos($d, '00') === 0) { $d = substr($d, 2); }
    if ((strlen($d) === 11 || strlen($d) === 13) && strpos($d, '229') === 0) { $d = substr($d, 3); }
    return $d;
}
function valid_phone($d) { return (bool)preg_match('/^[0-9]{8,15}$/', $d); }

function find_donateur_by_phone($numero) {
    global $pdo;
    $st = $pdo->prepare('SELECT * FROM Donateur WHERE numero = ?');
    $st->execute(array($numero));
    $d = $st->fetch();
    return $d ? $d : null;
}

// ---------------------------------------------------------------- Formats
function parse_amount($s) {
    $s = str_replace(array(' ', "\xC2\xA0", "\xE2\x80\xAF"), '', (string)$s);
    $s = str_replace(',', '.', $s);
    return is_numeric($s) ? (float)$s : null;
}
function valid_date($s) {
    return (bool)preg_match('/^\d{4}-\d{2}-\d{2}$/', $s) && checkdate((int)substr($s, 5, 2), (int)substr($s, 8, 2), (int)substr($s, 0, 4));
}
function fcfa($n) {
    if ($n === null || $n === '') { return '—'; }
    $n = (float)$n;
    $dec = (floor($n) == $n) ? 0 : 2;
    return number_format($n, $dec, ',', "\xC2\xA0") . "\xC2\xA0FCFA";
}
function nombre($n) { return number_format((float)$n, 0, ',', "\xC2\xA0"); }
function date_fr($d) {
    if (!$d) { return '—'; }
    $t = strtotime($d);
    return $t ? date('d/m/Y', $t) : e($d);
}
function montant_don($don) {
    return $don['type_don'] === 'Financier' ? fcfa($don['montant']) : '—';
}

// ---------------------------------------------------------------- Pagination
function paginate($total, $perPage, $page) {
    $pages = max(1, (int)ceil($total / $perPage));
    $page  = max(1, min($pages, (int)$page));
    return array('page' => $page, 'pages' => $pages, 'offset' => ($page - 1) * $perPage, 'per' => $perPage, 'total' => $total);
}
function pager_html($p, $params = array()) {
    if ($p['pages'] <= 1) { return ''; }
    unset($params['p']);
    $out = '<nav class="pager" aria-label="Pagination">';
    for ($i = 1; $i <= $p['pages']; $i++) {
        $q = http_build_query(array_merge($params, array('p' => $i)));
        $out .= '<a href="?' . e($q) . '" class="' . ($i === $p['page'] ? 'on' : '') . '">' . $i . '</a>';
    }
    return $out . '</nav>';
}

// ---------------------------------------------------------------- Besoins / répartitions
function don_solde($idDon, $excludeRepId = 0) {
    global $pdo;
    $st = $pdo->prepare(
        'SELECT d.id_don, d.montant, d.type_don, d.detail_don, d.date_don,
                COALESCE((SELECT SUM(r.montant_utilise) FROM repartition r
                          WHERE r.id_don = d.id_don AND r.id_repartition <> ?), 0) AS utilise
         FROM Dons d WHERE d.id_don = ?'
    );
    $st->execute(array((int)$excludeRepId, (int)$idDon));
    $r = $st->fetch();
    if (!$r) { return null; }
    $r['reste'] = (float)$r['montant'] - (float)$r['utilise'];
    return $r;
}

// Valide le formulaire de répartition. Retourne array($erreurs, $donnees).
function repartition_valider($excludeRepId = 0) {
    $err = array();
    $idDon = (int)post('id_don', 0);
    $usage = post('USA');
    $date  = post('date_utilisation');
    $montant = 0.0;

    $don = $idDon ? don_solde($idDon, $excludeRepId) : null;
    if (!$don) { $err[] = 'Choisis un don valide.'; }

    if ($usage === '' || str_len($usage) < 3) { $err[] = "Décris l'usage du don (3 caractères minimum)."; }
    if (str_len($usage) > 200) { $err[] = "L'usage est trop long (200 caractères maximum)."; }
    if (!valid_date($date)) { $err[] = "La date d'utilisation est invalide."; }

    if ($don && $don['type_don'] === 'Financier') {
        $m = parse_amount(post('montant_utilise'));
        if ($m === null || $m <= 0) {
            $err[] = 'Indique le montant utilisé (supérieur à 0).';
        } elseif ($m > $don['reste'] + 0.005) {
            $err[] = 'Ce montant dépasse le solde restant de ce don (' . fcfa(max(0, $don['reste'])) . ').';
        } else {
            $montant = $m;
        }
    }
    return array($err, array('id_don' => $idDon, 'USA' => $usage, 'montant_utilise' => $montant, 'date_utilisation' => $date));
}

// ---------------------------------------------------------------- Filtres de la liste des dons (admin + export)
function dons_filtres() {
    $where = array('1=1');
    $params = array();
    $type = get('type');
    if (in_array($type, types_don(), true)) { $where[] = 'd.type_don = ?'; $params[] = $type; }
    $du = get('du'); $au = get('au');
    if (valid_date($du)) { $where[] = 'd.date_don >= ?'; $params[] = $du; }
    if (valid_date($au)) { $where[] = 'd.date_don <= ?'; $params[] = $au; }
    $q = get('q');
    if ($q !== '') {
        $where[] = '(dn.nom LIKE ? OR dn.prenom LIKE ? OR dn.numero LIKE ? OR d.detail_don LIKE ?)';
        $like = '%' . $q . '%';
        array_push($params, $like, $like, $like, $like);
    }
    return array(implode(' AND ', $where), $params);
}

// Liste des dons avec solde restant (pour les formulaires de répartition)
function dons_avec_solde($excludeRepId = 0) {
    global $pdo;
    $st = $pdo->prepare(
        'SELECT d.id_don, d.montant, d.type_don, d.detail_don, d.date_don, dn.nom, dn.prenom,
                d.montant - COALESCE((SELECT SUM(r.montant_utilise) FROM repartition r WHERE r.id_don = d.id_don AND r.id_repartition <> ?), 0) AS reste
         FROM Dons d JOIN Donateur dn ON dn.id = d.id_donateur ORDER BY d.date_don DESC, d.id_don DESC'
    );
    $st->execute(array((int)$excludeRepId));
    return $st->fetchAll();
}
