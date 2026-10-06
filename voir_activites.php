<?php
require_once __DIR__ . '/includes/functions.php';

$activites = array(
    array('assets/img/activites/activite-1.jpg', 'Peinture et créativité', 'Nos enfants participent à des ateliers de peinture pour développer leur créativité et leur expression artistique.', 'Un enfant dessine sur une feuille'),
    array('assets/img/activites/activite-2.jpg', 'Lecture et apprentissage', 'Ils adorent passer du temps à lire et à découvrir de nouvelles histoires dans notre bibliothèque.', 'Un enfant lit des livres dans l\'herbe'),
    array('assets/img/activites/activite-3.jpg', 'Jeux en plein air', 'Les jeux en plein air permettent à nos enfants de se divertir tout en développant leur motricité et leur esprit d\'équipe.', 'Danse en plein air au son des tambours'),
    array('assets/img/activites/activite-4.jpg', 'Sports et activités physiques', 'Nous organisons des activités sportives pour encourager nos enfants à rester actifs et à adopter un mode de vie sain.', 'Enfants jouant au football sur un terrain en terre'),
    array('assets/img/activites/activite-5.jpg', 'Ateliers de musique', 'La musique est un excellent moyen d\'expression, et nos enfants adorent jouer d\'instruments et chanter ensemble.', 'Un enfant assis près d\'un djembé'),
);

$pageTitle = 'Nos enfants en action';
$nav = 'activites';
require __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
  <span class="blob b1"></span><span class="blob b2"></span>
  <div class="wrap"><h1 class="rise">Nos enfants en action</h1><p class="rise" style="--d:.12s">Découvrez les activités auxquelles participent nos enfants grâce à votre soutien.</p></div>
</section>

<section class="section" style="padding-top:34px">
  <div class="wrap">
    <div class="videos">
      <div class="video tilt" data-tilt="3" data-reveal="zoom"><video data-autoplay muted loop playsinline controls preload="metadata"><source src="assets/media/video-1.mp4" type="video/mp4">Votre navigateur ne prend pas en charge la balise vidéo.</video></div>
      <div class="video tilt" data-tilt="3" data-reveal="zoom" style="--d:.12s"><video data-autoplay muted loop playsinline controls preload="metadata"><source src="assets/media/video-2.mp4" type="video/mp4">Votre navigateur ne prend pas en charge la balise vidéo.</video></div>
    </div>
  </div>
</section>

<section class="section" style="padding-top:0">
  <div class="wrap">
    <div class="section-head" data-reveal><span class="kicker">Au quotidien</span><h2><?= svg_icon('palette') ?> Nos activités</h2></div>
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
    <div class="band" style="margin-top:60px" data-reveal="zoom">
      <h2>Aidez-nous à continuer</h2><p>Vos dons financent ces activités et le quotidien des enfants.</p>
      <p style="margin-top:20px"><a class="btn btn-sun btn-lg" href="faire_un_don.php"><?= svg_icon('heart') ?> Faire un don</a></p>
    </div>
  </div>
</section>
<div class="lightbox" id="lightbox" role="dialog" aria-label="Image agrandie"><button type="button" aria-label="Fermer"><?= svg_icon('close') ?></button><img src="" alt=""></div>
<?php require __DIR__ . '/includes/footer.php'; ?>
