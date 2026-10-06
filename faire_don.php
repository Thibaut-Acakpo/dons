<?php
require_once __DIR__ . '/includes/functions.php';

$errors    = array();
$donateur  = current_donateur();                 // donateur déjà identifié dans cette session
$numeroPre = normalize_phone(get('numero'));     // numéro pré-rempli (donateur inconnu redirigé)
$typePre   = in_array(get('type'), types_don(), true) ? get('type') : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $type    = post('type_don');
    $detail  = post('detail_don');
    $dateDon = post('date_don');
    $numero  = $donateur ? $donateur['numero'] : normalize_phone(post('numero'));
    $nom     = post('nom');
    $prenom  = post('prenom');
    $sexe    = post('sexe');
    $montant = 0.0;

    // --- Identité (nouveau donateur uniquement)
    $existant = $donateur;
    if (!$existant) {
        if (!valid_phone($numero)) { $errors[] = 'Numéro de téléphone invalide (8 à 15 chiffres).'; }
        else { $existant = find_donateur_by_phone($numero); }
        if (!$existant) {
            if ($nom === '' || str_len($nom) > 60)       { $errors[] = 'Indique ton nom (60 caractères maximum).'; }
            if ($prenom === '' || str_len($prenom) > 60) { $errors[] = 'Indique ton prénom (60 caractères maximum).'; }
            if (!in_array($sexe, sexes(), true))         { $errors[] = 'Sélectionne ton sexe.'; }
        }
    }

    // --- Don
    if (!in_array($type, types_don(), true)) {
        $errors[] = 'Choisis un type de don.';
    } elseif ($type === 'Financier') {
        $m = parse_amount(post('montant'));
        if ($m === null || $m <= 0)       { $errors[] = 'Indique un montant supérieur à 0.'; }
        elseif ($m > 100000000)           { $errors[] = 'Ce montant est trop élevé : contacte-nous directement.'; }
        else                              { $montant = $m; }
        $detail = '';
    } else {
        foreach (array('périmé', 'dangereux', 'toxique', 'explosif', 'expiré', 'avarié') as $mot) {
            if (stripos($detail, $mot) !== false) { $errors[] = 'Ton don contient un élément interdit : « ' . $mot . ' ».'; break; }
        }
        if (str_len($detail) < 10) { $errors[] = 'Décris ton don plus précisément (10 caractères minimum).'; }
        elseif (!preg_match('/\p{L}{3,}/u', $detail)) { $errors[] = 'La description du don semble invalide.'; }
        if (str_len($detail) > 500) { $errors[] = 'La description est trop longue (500 caractères maximum).'; }
    }
    if (!valid_date($dateDon))          { $errors[] = 'La date du don est invalide.'; }
    elseif ($dateDon > date('Y-m-d'))   { $errors[] = 'La date du don ne peut pas être dans le futur.'; }
    if (post('certifie') === '')        { $errors[] = 'Tu dois certifier que ton don est conforme.'; }

    if (!$errors) {
        $pdo->beginTransaction();
        try {
            if ($existant) {
                $idDonateur = (int)$existant['id'];
                $nouveau = false;
            } else {
                $st = $pdo->prepare('INSERT INTO Donateur (nom, prenom, sexe, numero) VALUES (?, ?, ?, ?)');
                $st->execute(array($nom, $prenom, $sexe, $numero));
                $idDonateur = (int)$pdo->lastInsertId();
                $nouveau = true;
            }
            $st = $pdo->prepare('INSERT INTO Dons (montant, type_don, date_don, id_donateur, detail_don) VALUES (?, ?, ?, ?, ?)');
            $st->execute(array($montant, $type, $dateDon, $idDonateur, $detail));
            $idDon = (int)$pdo->lastInsertId();
            $pdo->commit();
        } catch (Exception $ex) {
            $pdo->rollBack();
            throw $ex;
        }
        session_regenerate_id(true);
        $_SESSION['dons_ok'][] = $idDon;                       // autorise le reçu de CE don
        if ($donateur || $nouveau) { $_SESSION['donateur_id'] = $idDonateur; }
        redirect('merci.php?don=' . $idDon);
    }
}

$pageTitle = 'Faire un don';
$nav = '';
$typeCoche = isset($_POST['type_don']) ? post('type_don') : $typePre;
require __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
  <span class="blob b1"></span><span class="blob b2"></span>
  <div class="wrap"><h1 class="rise">Faire un don</h1><p class="rise" style="--d:.12s">Quelques informations, et votre générosité rejoint nos enfants.</p></div>
</section>
<div class="wrap">
  <form class="card form-card" method="post" data-don-form data-type="<?= e($typeCoche) ?>">
    <?= csrf_field() ?>
    <?php if ($errors): ?>
      <div class="alert alert-err" role="alert">Merci de corriger :<ul><?php foreach ($errors as $er): ?><li><?= e($er) ?></li><?php endforeach; ?></ul></div>
    <?php endif; ?>

    <?php if ($donateur): ?>
      <div class="alert alert-info">Bienvenue de retour, <strong><?= e($donateur['prenom'] . ' ' . $donateur['nom']) ?></strong> ! Merci pour votre fidélité.
        <a href="faire_un_don.php?reset=1">Ce n'est pas vous ?</a></div>
    <?php else: ?>
      <div class="row2">
        <div class="field"><label for="nom">Nom</label><input class="input" id="nom" name="nom" maxlength="60" value="<?= old('nom') ?>" required></div>
        <div class="field"><label for="prenom">Prénom</label><input class="input" id="prenom" name="prenom" maxlength="60" value="<?= old('prenom') ?>" required></div>
      </div>
      <div class="row2">
        <div class="field"><label for="numero">Téléphone</label>
          <input class="input" id="numero" name="numero" type="tel" inputmode="tel" placeholder="Ex : 01 56 03 48 10" value="<?= isset($_POST['numero']) ? old('numero') : e($numeroPre) ?>" required>
          <div class="hint">Il vous servira à retrouver vos dons.</div></div>
        <div class="field"><label for="sexe">Sexe</label>
          <select class="input" id="sexe" name="sexe" required>
            <option value="">— Sélectionnez —</option>
            <?php foreach (sexes() as $s): ?><option value="<?= e($s) ?>" <?= post('sexe') === $s ? 'selected' : '' ?>><?= e($s) ?></option><?php endforeach; ?>
          </select></div>
      </div>
    <?php endif; ?>

    <hr style="border:0;border-top:1px dashed var(--line);margin:8px 0 22px">
    <div class="field">
      <span class="lab">Type de don</span>
      <div class="type-pick">
        <?php foreach (types_don() as $t): ?>
          <label><input type="radio" name="type_don" value="<?= e($t) ?>" <?= $typeCoche === $t ? 'checked' : '' ?> required><span><b><?= type_icon($t) ?></b><?= e(type_label($t)) ?></span></label>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="field f-fin">
      <label for="montant">Montant du don (FCFA)</label>
      <input class="input" id="montant" name="montant" type="text" inputmode="decimal" placeholder="Ex : 10 000" value="<?= old('montant') ?>">
      <div class="chips"><?php foreach (array(1000, 5000, 10000, 25000, 50000) as $a): ?><button type="button" class="chip" data-amount="<?= $a ?>"><?= nombre($a) ?> F</button><?php endforeach; ?></div>
    </div>
    <div class="field f-detail">
      <label for="detail_don" data-detail-label>Détails du don :</label>
      <textarea class="input" id="detail_don" name="detail_don" rows="3" maxlength="500"><?= old('detail_don') ?></textarea>
    </div>

    <div class="field">
      <label for="date_don">Date du don</label>
      <input class="input" id="date_don" name="date_don" type="date" max="<?= date('Y-m-d') ?>" value="<?= isset($_POST['date_don']) ? old('date_don') : date('Y-m-d') ?>" required>
    </div>
    <div class="field">
      <label class="check"><input type="checkbox" name="certifie" value="1" <?= post('certifie') !== '' ? 'checked' : '' ?> required>
        <span>Je certifie que les produits donnés ne sont ni périmés, ni dangereux, et j'accepte la <a href="politique.php" target="_blank" rel="noopener">politique de dons</a>.</span></label>
    </div>
    <button class="btn btn-sun btn-lg btn-block" type="submit"><?= svg_icon('heart') ?> Envoyer mon don</button>
    <p class="center" style="margin:18px 0 0"><a href="index.php"><?= svg_icon('arrow-left') ?> Retour à l'accueil</a></p>
  </form>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
