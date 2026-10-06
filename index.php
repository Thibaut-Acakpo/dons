<?php
require_once __DIR__ . '/includes/functions.php';

// Statistiques publiques (transparence)
$totalFin   = (float)$pdo->query("SELECT COALESCE(SUM(montant),0) FROM Dons WHERE type_don = 'Financier'")->fetchColumn();
$nbDons     = (int)$pdo->query('SELECT COUNT(*) FROM Dons')->fetchColumn();
$nbDonateurs = (int)$pdo->query('SELECT COUNT(*) FROM Donateur')->fetchColumn();
$utilise    = (float)$pdo->query('SELECT COALESCE(SUM(montant_utilise),0) FROM repartition')->fetchColumn();
$besoins    = $pdo->query("SELECT * FROM Besoins ORDER BY FIELD(priorite,'haute','moyenne','basse'), date_requete DESC LIMIT 3")->fetchAll();

$pageTitle = 'Accueil';
$nav = 'home';
require __DIR__ . '/includes/header.php';
?>
<section class="hero">
  <canvas id="fx" aria-hidden="true"></canvas>
  <div class="wrap hero-grid">
    <div>
      <span class="eyebrow rise" style="--d:.05s"><i></i> Orphelinat du Centre YƐtƐn</span>
      <h1 class="rise" style="--d:.15s">Construisons ensemble <em>un avenir</em> meilleur pour nos enfants</h1>
      <p class="lead rise" style="--d:.28s">Grâce à votre générosité, chaque enfant est nourri, soigné, éduqué et entouré. Donnez en quelques minutes et suivez l'utilisation de votre don en toute transparence.</p>
      <div class="hero-actions rise" style="--d:.4s">
        <a class="btn btn-sun btn-lg" href="faire_un_don.php"><?= svg_icon('heart') ?> Je fais un don</a>
        <a class="btn btn-light btn-lg" href="besoins.php">Voir les besoins</a>
      </div>
    </div>
    <div class="hero-stage" aria-hidden="true">
      <div class="float a" data-depth="18"><b data-count="<?= (int)$totalFin ?>" data-suffix=" F">0</b><span>dons financiers reçus</span></div>
      <div class="float b" data-depth="30"><b data-count="<?= $nbDonateurs ?>">0</b><span>donateurs généreux</span></div>
      <div class="float c" data-depth="12"><b data-count="<?= $nbDons ?>">0</b><span>dons enregistrés</span></div>
    </div>
  </div>
</section>

<section class="section">
  <div class="wrap">
    <div class="section-head" data-reveal>
      <span class="kicker">Nos enfants, nos sourires</span>
      <h2>Chaque sourire est le fruit de votre soutien</h2>
      <p class="muted">Quelques instants de vie au Centre YƐtƐn.</p>
    </div>
    <div class="grid g3">
      <a class="photo tilt" data-tilt="6" data-reveal="zoom" href="voir_activites.php"><img src="assets/img/accueil/enfants-1.jpg" alt="Enfants souriants en classe" loading="lazy"></a>
      <a class="photo tilt" data-tilt="6" data-reveal="zoom" style="--d:.12s" href="voir_activites.php"><img src="assets/img/accueil/enfants-2.jpg" alt="Enfants montrant fièrement un maillot rouge" loading="lazy"></a>
      <a class="photo tilt" data-tilt="6" data-reveal="zoom" style="--d:.24s" href="voir_activites.php"><img src="assets/img/accueil/enfants-3.jpg" alt="Enfants qui s'enlacent" loading="lazy"></a>
    </div>
  </div>
</section>

<section class="section" style="padding-top:0">
  <div class="wrap">
    <div class="section-head" data-reveal>
      <span class="kicker">Simple et transparent</span>
      <h2>Comment ça marche ?</h2>
    </div>
    <div class="grid g3">
      <article class="card step tilt" data-reveal><span class="n">1</span><h3>Choisissez votre don</h3><p class="muted">Alimentaire, vestimentaire, médical ou financier : donnez ce qui compte pour vous.</p></article>
      <article class="card step tilt" data-reveal style="--d:.12s"><span class="n">2</span><h3>Enregistrez-le</h3><p class="muted">Un formulaire rapide, identifié par votre numéro de téléphone. Aucun compte à créer.</p></article>
      <article class="card step tilt" data-reveal style="--d:.24s"><span class="n">3</span><h3>Suivez son usage</h3><p class="muted">Retrouvez l'historique de vos dons et à quoi ils ont servi, avec un reçu à imprimer.</p></article>
    </div>
  </div>
</section>

<?php if ($besoins): ?>
<section class="section" style="padding-top:0">
  <div class="wrap">
    <div class="section-head" data-reveal>
      <span class="kicker">À soutenir en priorité</span>
      <h2>Nos besoins du moment</h2>
    </div>
    <div class="grid g3">
      <?php foreach ($besoins as $i => $b): ?>
        <article class="card need tilt" data-reveal style="--d:<?= $i * 0.12 ?>s">
          <div class="top"><span class="ico"><?= type_icon($b['type_besoin']) ?></span><span class="badge b-<?= e($b['priorite']) ?>">Priorité <?= e($b['priorite']) ?></span></div>
          <h3><?= e($b['type_besoin']) ?></h3>
          <p class="muted"><?= e($b['description']) ?></p>
          <div class="foot">
            <span class="amount"><?= $b['montant_estimatif'] !== null ? e(fcfa($b['montant_estimatif'])) : '' ?></span>
            <a class="btn btn-sm" href="faire_don.php?type=<?= e(besoin_vers_don($b['type_besoin'])) ?>">Contribuer</a>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
    <p class="center" style="margin-top:28px"><a class="btn btn-ghost" href="besoins.php">Voir tous les besoins <?= svg_icon('arrow-right') ?></a></p>
  </div>
</section>
<?php endif; ?>

<section class="section" style="padding-top:0">
  <div class="wrap">
    <div class="grid g4">
      <div class="card stat tilt" data-reveal><span class="ico"><?= svg_icon('coins') ?></span><div class="num" data-count="<?= (int)$totalFin ?>" data-suffix=" F">0</div><div class="lbl">Dons financiers collectés</div></div>
      <div class="card stat tilt" data-reveal style="--d:.1s"><span class="ico"><?= svg_icon('receipt') ?></span><div class="num" data-count="<?= (int)$utilise ?>" data-suffix=" F">0</div><div class="lbl">Déjà utilisés pour les enfants</div></div>
      <div class="card stat tilt" data-reveal style="--d:.2s"><span class="ico"><?= svg_icon('users') ?></span><div class="num" data-count="<?= $nbDonateurs ?>">0</div><div class="lbl">Donateurs</div></div>
      <div class="card stat tilt" data-reveal style="--d:.3s"><span class="ico"><?= svg_icon('gift') ?></span><div class="num" data-count="<?= $nbDons ?>">0</div><div class="lbl">Dons reçus</div></div>
    </div>
  </div>
</section>

<section class="section" style="padding-top:0">
  <div class="wrap">
    <div class="band" data-reveal="zoom">
      <h2>Prêt à changer une vie ?</h2>
      <p>Votre geste, même modeste, fait une vraie différence pour un enfant.</p>
      <p style="margin-top:22px"><a class="btn btn-sun btn-lg" href="faire_un_don.php">Je donne maintenant</a></p>
    </div>
  </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
