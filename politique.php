<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Politique de dons et confidentialité';
$nav = '';
require __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
  <span class="blob b1"></span><span class="blob b2"></span>
  <div class="wrap"><h1 class="rise">Politique de dons &amp; confidentialité</h1><p class="rise" style="--d:.12s">Ce que nous acceptons, ce que nous conservons, et comment nous protégeons vos informations.</p></div>
</section>
<div class="wrap">
  <article class="card" style="max-width:820px;margin:-40px auto 70px;position:relative;z-index:2">
    <h2>1. Les dons que nous acceptons</h2>
    <p>Nous acceptons des dons <strong>financiers</strong>, <strong>alimentaires</strong>, <strong>vestimentaires</strong> et <strong>médicaux</strong>. Pour la sécurité des enfants, les produits donnés doivent être en bon état : ni périmés, ni avariés, ni dangereux. En cochant la case de certification lors d'un don, vous confirmez que votre don respecte cette règle.</p>
    <p>Nous nous réservons le droit de refuser tout don qui ne serait pas conforme, notamment les médicaments périmés ou sans emballage d'origine.</p>

    <h2>2. Les informations que nous collectons</h2>
    <p>Lors d'un don, nous enregistrons votre <strong>nom, prénom, sexe et numéro de téléphone</strong>, ainsi que le type, la date et le détail de votre don. Si vous nous écrivez depuis la page Contact, votre message est conservé, et lié à votre fiche si vous vous êtes identifié.</p>

    <h2>3. Pourquoi nous les utilisons</h2>
    <p>Ces informations servent uniquement à enregistrer vos dons, à vous permettre de les retrouver grâce à votre numéro de téléphone, à vous délivrer un reçu et à rendre compte de l'utilisation des dons. Elles ne sont <strong>ni vendues ni cédées</strong> à des tiers.</p>

    <h2>4. Transparence sur l'utilisation des dons</h2>
    <p>Chaque don est suivi : l'orphelinat renseigne l'usage qui en est fait (achats, soins, fournitures…). Vous pouvez consulter cet historique depuis la page <a href="merci.php">Suivre mes dons</a>.</p>

    <h2>5. Vos droits</h2>
    <p>Vous pouvez demander à consulter, corriger ou supprimer vos informations personnelles en nous écrivant à <a href="mailto:<?= e(SITE_EMAIL) ?>"><?= e(SITE_EMAIL) ?></a> ou en appelant le <?= e(SITE_PHONE) ?>.</p>

    <h2>6. Votre numéro de téléphone</h2>
    <p>Votre numéro est votre identifiant : ne le communiquez pas à des personnes qui ne devraient pas consulter votre historique de dons. Sur un appareil partagé, pensez à utiliser « Changer de numéro » après consultation.</p>

    <p class="muted" style="margin-top:2em;font-size:.88rem">Dernière mise à jour : 05/10/2026. Ce texte est un modèle à faire valider par l'orphelinat.</p>
  </article>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
