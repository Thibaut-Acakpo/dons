<?php
/** Formulaire partagé répartition. Variables : $dons (avec reste), $v, $errors, $submitLabel */
?>
<form class="panel" method="post" style="max-width:720px" data-rep-form <?php if (!empty($confirmEdit)): ?>data-confirm="Vérifiez les informations ci-dessous avant de les enregistrer." data-confirm-title="Vérifier les modifications" data-confirm-label="Enregistrer" data-confirm-summary="form"<?php endif; ?>>
  <?= csrf_field() ?>
  <?php if (!empty($errors)): ?><div class="alert alert-err" role="alert"><ul><?php foreach ($errors as $er): ?><li><?= e($er) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
  <div class="field"><label for="id_don">Don concerné</label>
    <select class="input" id="id_don" name="id_don" required>
      <option value="">— Sélectionner un don —</option>
      <?php foreach ($dons as $d): ?>
        <option value="<?= (int)$d['id_don'] ?>" data-type="<?= e($d['type_don']) ?>" data-reste="<?= e(fcfa(max(0, $d['reste']))) ?>" <?= (int)$v['id_don'] === (int)$d['id_don'] ? 'selected' : '' ?>>
          #<?= (int)$d['id_don'] ?> · <?= e(date_fr($d['date_don'])) ?> · <?= e($d['prenom'] . ' ' . $d['nom']) ?> · <?= e(type_label($d['type_don'])) ?>
          <?= $d['type_don'] === 'Financier' ? '· ' . e(fcfa($d['montant'])) : '· ' . e(str_cut((string)$d['detail_don'], 40)) ?>
        </option>
      <?php endforeach; ?>
    </select></div>
  <div class="field"><label for="USA">Usage</label>
    <input class="input" id="USA" name="USA" maxlength="200" placeholder="Ex : achat de riz et d'huile" value="<?= e($v['USA']) ?>" required></div>
  <div class="field" data-montant-box>
    <label for="montant_utilise">Montant utilisé (FCFA)</label>
    <input class="input" id="montant_utilise" name="montant_utilise" type="text" inputmode="decimal" value="<?= e($v['montant_utilise']) ?>">
    <div class="hint" data-solde></div>
  </div>
  <div class="field"><label for="date_utilisation">Date d'utilisation</label>
    <input class="input" id="date_utilisation" name="date_utilisation" type="date" value="<?= e($v['date_utilisation']) ?>" required></div>
  <button class="btn" type="submit"><?= e($submitLabel) ?></button>
  <a class="btn btn-ghost" href="view_repartition.php">Annuler</a>
</form>
