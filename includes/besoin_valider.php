<?php
/** Valide le formulaire besoin. Retourne array($erreurs, $valeurs_pour_formulaire, $valeurs_sql). */
function besoin_valider() {
    $err = array();
    $v = array(
        'type_besoin' => post('type_besoin'), 'description' => post('description'), 'priorite' => post('priorite'),
        'date_requete' => post('date_requete'), 'montant_estimatif' => post('montant_estimatif'),
    );
    if (!in_array($v['type_besoin'], types_besoin(), true)) { $err[] = 'Type de besoin invalide.'; }
    if (!in_array($v['priorite'], priorites(), true))        { $err[] = 'Priorité invalide.'; }
    if (str_len($v['description']) < 5 || str_len($v['description']) > 1000) { $err[] = 'La description doit faire entre 5 et 1000 caractères.'; }
    if (!valid_date($v['date_requete']))                      { $err[] = 'Date invalide.'; }
    $montant = null;
    if ($v['montant_estimatif'] !== '') {
        $m = parse_amount($v['montant_estimatif']);
        if ($m === null || $m < 0 || $m > 999999999999) { $err[] = 'Montant estimatif invalide.'; } else { $montant = $m; }
    }
    return array($err, $v, array($v['type_besoin'], $v['description'], $v['priorite'], $v['date_requete'], $montant));
}
