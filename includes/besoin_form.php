<?php
/** Formulaire partagé ajout / modification d'un besoin. Variables : $v (valeurs), $errors, $submitLabel */
?>
<form class="panel" method="post" style="max-width:720px" <?php if (!empty($confirmEdit)): ?>data-confirm="Vérifiez les informations ci-dessous avant de les enregistrer." data-confirm-title="Vérifier les modifications" data-confirm-label="Enregistrer" data-confirm-summary="form"<?php endif; ?>>
  <?= csrf_field() ?>
  <?php if (!empty($errors)): ?><div class="alert alert-err" role="alert"><ul><?php foreach ($errors as $er): ?><li><?= e($er) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
  <div class="row2">
    <div class="field"><label for="type_besoin">Type de besoin</label>
      <select class="input" id="type_besoin" name="type_besoin" required>
        <?php foreach (types_besoin() as $t): ?><option value="<?= e($t) ?>" <?= $v['type_besoin'] === $t ? 'selected' : '' ?>><?= e($t) ?></option><?php endforeach; ?>
      </select></div>
    <div class="field"><label for="priorite">Priorité</label>
      <select class="input" id="priorite" name="priorite" required>
        <?php foreach (priorites() as $p): ?><option value="<?= e($p) ?>" <?= $v['priorite'] === $p ? 'selected' : '' ?>><?= e(ucfirst($p)) ?></option><?php endforeach; ?>
      </select></div>
  </div>
  <div class="field"><label for="description">Description</label>
    <textarea class="input" id="description" name="description" rows="4" maxlength="1000" required><?= e($v['description']) ?></textarea></div>
  <div class="row2">
    <div class="field"><label for="date_requete">Date de la requête</label>
      <input class="input" id="date_requete" name="date_requete" type="date" value="<?= e($v['date_requete']) ?>" required></div>
    <div class="field"><label for="montant_estimatif">Montant estimatif (FCFA)</label>
      <input class="input" id="montant_estimatif" name="montant_estimatif" type="text" inputmode="decimal" placeholder="Ex : 50000 (facultatif)" value="<?= e($v['montant_estimatif']) ?>"></div>
  </div>
  <button class="btn" type="submit"><?= e($submitLabel) ?></button>
  <a class="btn btn-ghost" href="manage_besoins.php">Annuler</a>
</form>
