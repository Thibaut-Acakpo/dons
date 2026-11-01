<?php
require_once __DIR__ . '/includes/functions.php';

// --- LOGIQUE ACCUEIL ---
$totalFin   = (float)$pdo->query("SELECT COALESCE(SUM(montant),0) FROM Dons WHERE type_don = 'Financier'")->fetchColumn();
$nbDons     = (int)$pdo->query('SELECT COUNT(*) FROM Dons')->fetchColumn();
$nbDonateurs = (int)$pdo->query('SELECT COUNT(*) FROM Donateur')->fetchColumn();
$utilise    = (float)$pdo->query('SELECT COALESCE(SUM(montant_utilise),0) FROM repartition')->fetchColumn();
$besoinsAccueil = $pdo->query("SELECT * FROM Besoins ORDER BY FIELD(priorite,'haute','moyenne','basse'), date_requete DESC LIMIT 3")->fetchAll();

// --- LOGIQUE BESOINS ---
$fType = in_array(get('type'), types_besoin(), true) ? get('type') : '';
$fPrio = in_array(get('priorite'), priorites(), true) ? get('priorite') : '';
$where = array(); $params = array();
if ($fType !== '') { $where[] = 'type_besoin = ?'; $params[] = $fType; }
if ($fPrio !== '') { $where[] = 'priorite = ?';    $params[] = $fPrio; }
$sql = 'SELECT * FROM Besoins' . ($where ? ' WHERE ' . implode(' AND ', $where) : '') . " ORDER BY FIELD(priorite,'haute','moyenne','basse'), date_requete DESC";
$st = $pdo->prepare($sql);
$st->execute($params);
$tousBesoins = $st->fetchAll();

// --- LOGIQUE ACTIVITES ---
$activites = array(
    array('assets/img/activites/activite-1.jpg', 'Peinture et créativité', 'Nos enfants participent à des ateliers de peinture pour développer leur créativité et leur expression artistique.', 'Un enfant dessine sur une feuille'),
    array('assets/img/activites/activite-2.jpg', 'Lecture et apprentissage', 'Ils adorent passer du temps à lire et à découvrir de nouvelles histoires dans notre bibliothèque.', 'Un enfant lit des livres dans l\'herbe'),
    array('assets/img/activites/activite-3.jpg', 'Jeux en plein air', 'Les jeux en plein air permettent à nos enfants de se divertir tout en développant leur motricité et leur esprit d\'équipe.', 'Danse en plein air au son des tambours'),
    array('assets/img/activites/activite-4.jpg', 'Sports et activités physiques', 'Nous organisons des activités sportives pour encourager nos enfants à rester actifs et à adopter un mode de vie sain.', 'Enfants jouant au football sur un terrain en terre'),
    array('assets/img/activites/activite-5.jpg', 'Ateliers de musique', 'La musique est un excellent moyen d\'expression, et nos enfants adorent jouer d\'instruments et chanter ensemble.', 'Un enfant assis près d\'un djembé'),
);

$pageTitle = 'Accueil';
$nav = 'home';
require __DIR__ . '/includes/header.php';
?>

<!-- ================= SECTION ACCUEIL ================= -->
<section id="accueil" class="hero">
  <canvas id="fx" aria-hidden="true"></canvas>
  <div class="wrap hero-grid">
    <div>
      <span class="eyebrow rise" style="--d:.05s"><i></i> Orphelinat du Centre YƐtƐn</span>
      <h1 class="rise" style="--d:.15s">Construisons ensemble <em>un avenir</em> meilleur pour nos enfants</h1>
      <p class="lead rise" style="--d:.28s">Grâce à votre générosité, chaque enfant est nourri, soigné, éduqué et entouré. Donnez en quelques minutes et suivez l'utilisation de votre don en toute transparence.</p>
      <div class="hero-actions rise" style="--d:.4s">
        <a class="btn btn-sun btn-lg" href="faire_un_don.php"><?= svg_icon('heart') ?> Je fais un don</a>
        <a class="btn btn-light btn-lg" href="#besoins">Voir les besoins</a>
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
      <a class="photo tilt" data-tilt="6" data-reveal="zoom" href="#activites"><img src="assets/img/accueil/enfants-1.jpg" alt="Enfants souriants en classe" loading="lazy"></a>
      <a class="photo tilt" data-tilt="6" data-reveal="zoom" style="--d:.12s" href="#activites"><img src="assets/img/accueil/enfants-2.jpg" alt="Enfants montrant fièrement un maillot rouge" loading="lazy"></a>
      <a class="photo tilt" data-tilt="6" data-reveal="zoom" style="--d:.24s" href="#activites"><img src="assets/img/accueil/enfants-3.jpg" alt="Enfants qui s'enlacent" loading="lazy"></a>
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

<!-- ================= SECTION BESOINS ================= -->
<section id="besoins" class="section" style="padding-top:60px; background:#f8fafc; border-top: 1px solid #e2e8f0;">
  <div class="wrap">
    <div class="section-head" data-reveal>
      <span class="kicker">À soutenir en priorité</span>
      <h2>Nos besoins prioritaires</h2>
      <p class="muted">Aidez-nous à mieux soutenir nos enfants : choisissez un besoin et contribuez directement.</p>
    </div>
    
    <!-- Filtres dynamiques -->
    <div class="chips" style="margin:0 0 8px; justify-content:center;" aria-label="Filtrer par type">
      <button type="button" class="chip on" data-filter-type="">Tous les types</button>
      <?php foreach (types_besoin() as $t): ?>
        <button type="button" class="chip" data-filter-type="<?= e($t) ?>"><?= type_icon($t) ?> <?= e($t) ?></button>
      <?php endforeach; ?>
    </div>
    <div class="chips" style="margin:0 0 30px; justify-content:center;" aria-label="Filtrer par priorité">
      <button type="button" class="chip on" data-filter-prio="">Toutes priorités</button>
      <?php foreach (priorites() as $p): ?>
        <button type="button" class="chip" data-filter-prio="<?= e($p) ?>">Priorité <?= e($p) ?></button>
      <?php endforeach; ?>
    </div>

    <!-- Liste des besoins avec attributs data pour le filtrage -->
    <?php if ($tousBesoins): ?>
      <div class="grid g3" id="besoins-grid">
        <?php foreach ($tousBesoins as $i => $b): ?>
          <article class="card need tilt" data-reveal data-type="<?= e($b['type_besoin']) ?>" data-prio="<?= e($b['priorite']) ?>" style="--d:<?= min($i, 5) * 0.07 ?>s">
            <div class="top"><span class="ico"><?= type_icon($b['type_besoin']) ?></span><span class="badge b-<?= e($b['priorite']) ?>">Priorité <?= e($b['priorite']) ?></span></div>
            <h3><?= e($b['type_besoin']) ?></h3>
            <p class="muted"><?= e($b['description']) ?></p>
            <p class="muted" style="font-size:.85rem">Demande du <?= e(date_fr($b['date_requete'])) ?></p>
            <div class="foot">
              <span class="amount"><?= $b['montant_estimatif'] !== null ? e(fcfa($b['montant_estimatif'])) : 'Montant libre' ?></span>
              <a class="btn btn-sm" href="faire_don.php?type=<?= e(besoin_vers_don($b['type_besoin'])) ?>"><?= svg_icon('heart') ?> Contribuer</a>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="card center"><p>Aucun besoin ne correspond à ce filtre pour l'instant.</p></div>
    <?php endif; ?>
  </div>
</section>

<!-- ================= SECTION ACTIVITES ================= -->
<section id="activites" class="section" style="padding-top:60px; border-top: 1px solid #e2e8f0;">
  <div class="wrap">
    <div class="section-head" data-reveal>
      <span class="kicker">Au quotidien</span>
      <h2>Nos enfants en action</h2>
      <p class="muted">Découvrez les activités auxquelles participent nos enfants grâce à votre soutien.</p>
    </div>
    
    <div class="videos" style="margin-bottom:40px">
      <div class="video tilt" data-tilt="3" data-reveal="zoom"><video data-autoplay muted loop playsinline controls preload="metadata"><source src="assets/media/video-1.mp4" type="video/mp4">Votre navigateur ne prend pas en charge la balise vidéo.</video></div>
      <div class="video tilt" data-tilt="3" data-reveal="zoom" style="--d:.12s"><video data-autoplay muted loop playsinline controls preload="metadata"><source src="assets/media/video-2.mp4" type="video/mp4">Votre navigateur ne prend pas en charge la balise vidéo.</video></div>
    </div>

    <div class="grid g3">
      <?php foreach ($activites as $i => $a): ?>
        <article class="card activity tilt" data-reveal style="--d:<?= ($i % 3) * 0.1 ?>s">
          <div class="photo" data-zoom="<?= e($a[0]) ?>" data-alt="<?= e($a[3]) ?>" role="button" tabindex="0" aria-label="Agrandir : <?= e($a[3]) ?>">
            <img src="<?= e($a[0]) ?>" alt="<?= e($a[3]) ?>" loading="lazy">
          </div>
          <div class="body"><h3><?= e($a[1]) ?></h3><p><?= e($a[2]) ?></p></div>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ================= SECTION CONTACT ================= -->
<section id="contact" class="section" style="padding-top:60px; border-top: 1px solid #e2e8f0; background:#f8fafc;">
  <div class="wrap">
    <div class="section-head" data-reveal>
      <h2>Contactez l'orphelinat</h2>
      <p class="muted">Un mot d'encouragement, une question, une proposition : nous lisons chaque message.</p>
    </div>
    
    <div class="grid g2" style="max-width:960px;margin:0 auto;align-items:start">
      
      <!-- Zone d'affichage du message de succès AJAX -->
      <div id="contact-flash" style="grid-column: 1 / -1;"></div>

      <form id="contact-form" class="card" method="post" action="contact.php">
        <?= csrf_field() ?>
        <h2>Votre message</h2>
        <div class="field">
          <label for="message">Laissez-nous un mot</label>
          <textarea class="input" id="message" name="message" rows="7" maxlength="2000" required></textarea>
        </div>
        <button class="btn btn-block" type="submit" id="contact-submit">Envoyer le message</button>
      </form>
      
      <aside class="card tilt" data-tilt="4">
        <h2>Nos coordonnées</h2>
        <p><?= svg_icon('mail') ?> <a href="mailto:<?= e(SITE_EMAIL) ?>"><?= e(SITE_EMAIL) ?></a></p>
        <p><?= svg_icon('phone') ?> <a href="tel:<?= e(preg_replace('/\s+/', '', SITE_PHONE)) ?>"><?= e(SITE_PHONE) ?></a></p>
        <p class="muted">Vous souhaitez remettre un don en nature ? Enregistrez-le d'abord sur le site, puis appelez-nous pour convenir d'un rendez-vous.</p>
        <a class="btn btn-sun" href="faire_un_don.php"><?= svg_icon('heart') ?> Faire un don</a>
      </aside>
    </div>
  </div>
</section>

<!-- ================= BANNIERE FINALE ================= -->
<section class="section" style="padding-top:0">
  <div class="wrap">
    <div class="band" data-reveal="zoom" style="margin-top:60px">
      <h2>Prêt à changer une vie ?</h2>
      <p>Votre geste, même modeste, fait une vraie différence pour un enfant.</p>
      <p style="margin-top:22px"><a class="btn btn-sun btn-lg" href="faire_un_don.php">Je donne maintenant</a></p>
    </div>
  </div>
</section>

<div class="lightbox" id="lightbox" role="dialog" aria-label="Image agrandie"><button type="button" aria-label="Fermer"><?= svg_icon('close') ?></button><img src="" alt=""></div>

<?php require __DIR__ . '/includes/footer.php'; ?>
