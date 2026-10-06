</main>
<footer class="footer">
  <div class="wrap">
    <div class="footer-grid">
      <div>
        <h4><?= e(SITE_NAME) ?></h4>
        <p>Chaque don, quelle que soit sa forme, aide à nourrir, éduquer et protéger nos enfants. Merci de faire partie de leur histoire.</p>
      </div>
      <div>
        <h4>Liens rapides</h4>
        <ul>
          <li><a href="faire_un_don.php">Faire un don</a></li>
          <li><a href="merci.php">Suivre mes dons</a></li>
          <li><a href="besoins.php">Voir les besoins</a></li>
          <li><a href="voir_activites.php">Nos enfants en action</a></li>
          <li><a href="politique.php">Politique de dons &amp; confidentialité</a></li>
        </ul>
      </div>
      <div>
        <h4>Nous contacter</h4>
        <ul>
          <li><?= svg_icon('mail') ?> <a href="mailto:<?= e(SITE_EMAIL) ?>"><?= e(SITE_EMAIL) ?></a></li>
          <li><?= svg_icon('phone') ?> <a href="tel:<?= e(preg_replace('/\s+/', '', SITE_PHONE)) ?>"><?= e(SITE_PHONE) ?></a></li>
          <li><a href="contact.php">Envoyer un message d'encouragement</a></li>
        </ul>
      </div>
    </div>
    <div class="footer-bottom">
      <span>&copy; <?= date('Y') ?> <?= e(SITE_NAME) ?> — Tous droits réservés</span>
      <a href="admin_login.php">Espace administrateur</a>
    </div>
  </div>
</footer>
<script src="assets/js/app.js?v=<?= filemtime(__DIR__ . '/../assets/js/app.js') ?>" defer></script>
</body>
</html>
