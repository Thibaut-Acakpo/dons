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
          <li><a href="index.php#besoins">Voir les besoins</a></li>
          <li><a href="index.php#activites">Nos enfants en action</a></li>
          <li><a href="politique.php">Politique de dons &amp; confidentialité</a></li>
        </ul>
      </div>
      <div>
        <h4>Nous contacter</h4>
        <ul>
          <li><?= svg_icon('mail') ?> <a href="mailto:<?= e(SITE_EMAIL) ?>"><?= e(SITE_EMAIL) ?></a></li>
          <li><?= svg_icon('phone') ?> <a href="tel:<?= e(preg_replace('/\s+/', '', SITE_PHONE)) ?>"><?= e(SITE_PHONE) ?></a></li>
          <li><a href="index.php#contact">Envoyer un message d'encouragement</a></li>
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
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // --- 1. Défilement fluide pour les liens d'ancrage ---
    document.querySelectorAll('a[href*="#"]').forEach(anchor => {
      anchor.addEventListener('click', function (e) {
        const href = this.getAttribute('href');
        if (window.location.pathname.indexOf('index.php') !== -1 || href.indexOf('index.php') !== -1) {
          const targetId = href.split('#')[1];
          if (!targetId) return;
          const targetElement = document.getElementById(targetId);
          if (targetElement) {
            e.preventDefault();
            targetElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
            history.pushState(null, null, '#' + targetId);
          }
        }
      });
    });

    // --- 2. Envoi du formulaire de contact en AJAX ---
    var contactForm = document.getElementById('contact-form');
    if (contactForm) {
      contactForm.addEventListener('submit', function(e) {
        e.preventDefault();
        var submitBtn = document.getElementById('contact-submit');
        var flashZone = document.getElementById('contact-flash');
        var originalText = submitBtn.textContent;
        
        submitBtn.disabled = true;
        submitBtn.textContent = 'Envoi en cours...';
        flashZone.innerHTML = '';
        
        var formData = new FormData(contactForm);
        
        fetch(contactForm.action, {
          method: 'POST',
          body: formData
        })
        .then(function(response) { return response.json(); })
        .then(function(data) {
          submitBtn.disabled = false;
          submitBtn.textContent = originalText;
          
          if (data.success) {
            flashZone.innerHTML = '<div class="alert alert-ok" role="status">' + data.message + '</div>';
            contactForm.reset();
          } else {
            flashZone.innerHTML = '<div class="alert alert-err" role="status">' + data.message + '</div>';
          }
        })
        .catch(function(error) {
          submitBtn.disabled = false;
          submitBtn.textContent = originalText;
          flashZone.innerHTML = '<div class="alert alert-err" role="status">Une erreur est survenue. Réessaie.</div>';
        });
      });
    }

    // --- 3. Filtrage dynamique des besoins ---
    var filterType = '';
    var filterPrio = '';
    var needCards = document.querySelectorAll('#besoins-grid article.need');
    var btnTypes = document.querySelectorAll('[data-filter-type]');
    var btnPrios = document.querySelectorAll('[data-filter-prio]');

    function applyFilters() {
      var visibleCount = 0;
      needCards.forEach(function(card) {
        var cardType = card.getAttribute('data-type');
        var cardPrio = card.getAttribute('data-prio');
        
        var matchType = (filterType === '' || cardType === filterType);
        var matchPrio = (filterPrio === '' || cardPrio === filterPrio);
        
        if (matchType && matchPrio) {
          card.style.display = '';
          visibleCount++;
        } else {
          card.style.display = 'none';
        }
      });

      // Afficher un message si aucun résultat (optionnel)
      var grid = document.getElementById('besoins-grid');
      if (grid) {
        var emptyMsg = document.getElementById('besoins-empty');
        if (!emptyMsg) {
          emptyMsg = document.createElement('div');
          emptyMsg.id = 'besoins-empty';
          emptyMsg.className = 'card center';
          emptyMsg.style.gridColumn = '1 / -1';
          emptyMsg.innerHTML = '<p>Aucun besoin ne correspond à ce filtre pour l\'instant.</p>';
          grid.parentNode.appendChild(emptyMsg);
        }
        emptyMsg.style.display = visibleCount === 0 ? '' : 'none';
      }
    }

    // Clics sur les filtres de Type
    btnTypes.forEach(function(btn) {
      btn.addEventListener('click', function() {
        btnTypes.forEach(function(b) { b.classList.remove('on'); });
        this.classList.add('on');
        filterType = this.getAttribute('data-filter-type');
        applyFilters();
      });
    });

    // Clics sur les filtres de Priorité
    btnPrios.forEach(function(btn) {
      btn.addEventListener('click', function() {
        btnPrios.forEach(function(b) { b.classList.remove('on'); });
        this.classList.add('on');
        filterPrio = this.getAttribute('data-filter-prio');
        applyFilters();
      });
    });
  });
</script>
</body>
</html>
