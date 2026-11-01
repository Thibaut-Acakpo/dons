/* Gestion-don — comportements communs (aucune dépendance externe) */
(function () {
  'use strict';
  var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var $ = function (s, c) { return (c || document).querySelector(s); };
  var $$ = function (s, c) { return Array.prototype.slice.call((c || document).querySelectorAll(s)); };

  /* ---- En-tête : ombre au défilement + menu mobile ---- */
  var topbar = $('#topbar');
  if (topbar) {
    var onScroll = function () { topbar.classList.toggle('stuck', window.scrollY > 8); };
    window.addEventListener('scroll', onScroll, { passive: true }); onScroll();
  }
  var toggle = $('[data-nav-toggle]');
  if (toggle) {
    toggle.addEventListener('click', function () {
      var open = $('#nav').classList.toggle('open');
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
  }
  /* ---- Menu latéral de l'administration : ouverture, fermeture et accessibilité ---- */
  var side = $('#side');
  var sideBtn = $('[data-side-toggle]');
  var sideBackdrop = $('[data-side-backdrop]');

  function closeAdminMenu(returnFocus) {
    if (!side || !sideBtn) return;
    side.classList.remove('open');
    if (sideBackdrop) sideBackdrop.classList.remove('open');
    sideBtn.setAttribute('aria-expanded', 'false');
    sideBtn.setAttribute('aria-label', 'Ouvrir le menu');
    document.body.classList.remove('admin-menu-open');
    if (returnFocus) sideBtn.focus();
  }

  function openAdminMenu() {
    if (!side || !sideBtn) return;
    side.classList.add('open');
    if (sideBackdrop) sideBackdrop.classList.add('open');
    sideBtn.setAttribute('aria-expanded', 'true');
    sideBtn.setAttribute('aria-label', 'Fermer le menu');
    document.body.classList.add('admin-menu-open');
    var firstLink = $('a', side);
    if (firstLink) firstLink.focus();
  }

  if (side && sideBtn) {
    sideBtn.addEventListener('click', function () {
      if (side.classList.contains('open')) closeAdminMenu(false);
      else openAdminMenu();
    });

    if (sideBackdrop) {
      sideBackdrop.addEventListener('click', function () { closeAdminMenu(true); });
    }

    $$('a', side).forEach(function (link) {
      link.addEventListener('click', function () { closeAdminMenu(false); });
    });

    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape' && side.classList.contains('open')) {
        closeAdminMenu(true);
      }
    });

    window.addEventListener('resize', function () {
      if (window.innerWidth > 900 && side.classList.contains('open')) {
        closeAdminMenu(false);
      }
    });
  }

  /* ---- Apparition au défilement ---- */
  var revealEls = $$('[data-reveal],.bar,.months');
  if ('IntersectionObserver' in window && !reduce) {
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (en) {
        if (en.isIntersecting) { en.target.classList.add('in'); io.unobserve(en.target); }
      });
    }, { threshold: 0.15, rootMargin: '0px 0px -40px 0px' });
    revealEls.forEach(function (el) { io.observe(el); });
  } else {
    revealEls.forEach(function (el) { el.classList.add('in'); });
  }

  /* ---- Compteurs animés ---- */
  function fmt(n) { return Math.round(n).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '\u00A0'); }
  function countUp(el) {
    var target = parseFloat(el.getAttribute('data-count')) || 0;
    var suffix = el.getAttribute('data-suffix') || '';
    if (reduce || target === 0) { el.textContent = fmt(target) + suffix; return; }
    var t0 = null, dur = 1600;
    function step(ts) {
      if (!t0) t0 = ts;
      var p = Math.min(1, (ts - t0) / dur), eased = 1 - Math.pow(1 - p, 4);
      el.textContent = fmt(target * eased) + suffix;
      if (p < 1) requestAnimationFrame(step);
    }
    requestAnimationFrame(step);
  }
  var counters = $$('[data-count]');
  if ('IntersectionObserver' in window) {
    var cio = new IntersectionObserver(function (entries) {
      entries.forEach(function (en) { if (en.isIntersecting) { countUp(en.target); cio.unobserve(en.target); } });
    }, { threshold: 0.4 });
    counters.forEach(function (c) { cio.observe(c); });
  } else { counters.forEach(countUp); }

  /* ---- Cartes 3D qui suivent la souris ---- */
  if (!reduce && window.matchMedia('(hover: hover)').matches) {
    $$('.tilt').forEach(function (el) {
      var max = parseFloat(el.getAttribute('data-tilt')) || 7;
      el.addEventListener('mousemove', function (e) {
        var r = el.getBoundingClientRect(), x = (e.clientX - r.left) / r.width, y = (e.clientY - r.top) / r.height;
        el.style.transform = 'perspective(900px) rotateX(' + ((0.5 - y) * max).toFixed(2) + 'deg) rotateY(' + ((x - 0.5) * max * 1.4).toFixed(2) + 'deg) translateZ(6px)';
        el.style.setProperty('--mx', (x * 100) + '%'); el.style.setProperty('--my', (y * 100) + '%');
      });
      el.addEventListener('mouseleave', function () { el.style.transform = ''; });
    });
  }

  /* ---- Effet de vague (ripple) sur les boutons ---- */
  $$('.btn').forEach(function (b) {
    b.addEventListener('click', function (e) {
      if (reduce) return;
      var r = b.getBoundingClientRect(), s = Math.max(r.width, r.height), d = document.createElement('span');
      d.className = 'ripple'; d.style.width = d.style.height = s + 'px';
      d.style.left = (e.clientX - r.left - s / 2) + 'px'; d.style.top = (e.clientY - r.top - s / 2) + 'px';
      b.appendChild(d); setTimeout(function () { d.remove(); }, 650);
    });
  });

  /* ---- Hero : lucioles (canvas) + parallaxe ---- */
  var cv = $('#fx');
  if (cv && !reduce) {
    var ctx = cv.getContext('2d'), W, H, parts = [], mx = 0, my = 0, running = true, DPR = Math.min(2, window.devicePixelRatio || 1);
    function resize() {
      var r = cv.getBoundingClientRect(); W = r.width; H = r.height;
      cv.width = W * DPR; cv.height = H * DPR; ctx.setTransform(DPR, 0, 0, DPR, 0, 0);
      var n = Math.round(Math.min(70, W / 16)); parts = [];
      for (var i = 0; i < n; i++) parts.push(spawn(true));
    }
    function spawn(anywhere) {
      return { x: Math.random() * W, y: anywhere ? Math.random() * H : H + 20, r: 1 + Math.random() * 3.2,
        v: 0.15 + Math.random() * 0.55, a: Math.random() * Math.PI * 2, s: 0.4 + Math.random() * 1.2,
        warm: Math.random() > 0.45, d: 0.3 + Math.random() * 0.9 };
    }
    function draw() {
      if (running) {
        ctx.clearRect(0, 0, W, H);
        for (var i = 0; i < parts.length; i++) {
          var p = parts[i]; p.y -= p.v; p.a += 0.012 * p.s;
          var x = p.x + Math.sin(p.a) * 18 + mx * 22 * p.d, y = p.y + my * 14 * p.d;
          var life = Math.min(1, (H - p.y) / (H * 0.25), p.y / (H * 0.3)); life = Math.max(0, life);
          var g = ctx.createRadialGradient(x, y, 0, x, y, p.r * 5);
          var c = p.warm ? '255,200,90' : '210,235,255';
          g.addColorStop(0, 'rgba(' + c + ',' + (0.85 * life) + ')'); g.addColorStop(1, 'rgba(' + c + ',0)');
          ctx.fillStyle = g; ctx.beginPath(); ctx.arc(x, y, p.r * 5, 0, 6.2832); ctx.fill();
          if (p.y < -20) parts[i] = spawn(false);
        }
      }
      requestAnimationFrame(draw);
    }
    resize(); draw();
    window.addEventListener('resize', resize);
    if ('IntersectionObserver' in window) {
      new IntersectionObserver(function (en) { running = en[0].isIntersecting; }).observe(cv);
    }
    var hero = cv.parentNode, layers = $$('[data-depth]', hero);
    hero.addEventListener('mousemove', function (e) {
      var r = hero.getBoundingClientRect();
      mx = (e.clientX - r.left) / r.width - 0.5; my = (e.clientY - r.top) / r.height - 0.5;
      layers.forEach(function (l) {
        var d = parseFloat(l.getAttribute('data-depth')) || 10;
        l.style.transform = 'translate3d(' + (-mx * d).toFixed(1) + 'px,' + (-my * d).toFixed(1) + 'px,0)';
      });
    });
  }

  /* ---- Formulaire de don : champs selon le type ---- */
  var don = $('[data-don-form]');
  if (don) {
    var ph = { Alimentaire: 'Ex : 50 kg de riz, 10 cartons de lait, huile…', Vestimentaire: 'Ex : habits pour enfants de 6 à 12 ans, chaussures…', Medical: 'Ex : médicaments non périmés, pansements, moustiquaires…' };
    var area = $('textarea[name=detail_don]', don), lab = $('[data-detail-label]', don);
    var setType = function (t) {
      don.setAttribute('data-type', t || '');
      if (area && ph[t]) { area.placeholder = ph[t]; }
      if (lab && t) { lab.textContent = 'Détails du don ' + ({ Alimentaire: 'alimentaire', Vestimentaire: 'vestimentaire', Medical: 'médical' }[t] || '') + ' :'; }
    };
    $$('input[name=type_don]', don).forEach(function (r) { r.addEventListener('change', function () { setType(r.value); }); if (r.checked) setType(r.value); });
    $$('[data-amount]', don).forEach(function (c) {
      c.addEventListener('click', function () { var i = $('input[name=montant]', don); i.value = c.getAttribute('data-amount'); i.focus(); });
    });
  }

  /* ---- Formulaire de répartition : montant seulement pour un don financier ---- */
  var rep = $('[data-rep-form]');
  if (rep) {
    var sel = $('select[name=id_don]', rep), box = $('[data-montant-box]', rep), hint = $('[data-solde]', rep);
    var upd = function () {
      var o = sel.options[sel.selectedIndex], fin = o && o.getAttribute('data-type') === 'Financier';
      box.style.display = fin ? 'block' : 'none';
      if (hint) { hint.textContent = fin ? 'Solde restant sur ce don : ' + o.getAttribute('data-reste') : ''; }
    };
    sel.addEventListener('change', upd); upd();
  }

  /* ---- Confirmation avant action sensible ---- */
  var confirmationOpen = false;
  function askConfirmation(el) {
    var message = el.getAttribute('data-confirm') || 'Confirmer cette action ?';
    if (!document.createElement('dialog').showModal) { return Promise.resolve(window.confirm(message)); }
    if (confirmationOpen) { return Promise.resolve(false); }
    confirmationOpen = true;
    return new Promise(function (resolve) {
      var dialog = document.createElement('dialog');
      var content = document.createElement('div');
      var title = document.createElement('h2');
      var text = document.createElement('p');
      var summary = null;
      var actions = document.createElement('div');
      var cancel = document.createElement('button');
      var confirm = document.createElement('button');
      var previousFocus = document.activeElement;
      var settled = false;

      dialog.className = 'action-confirm';
      title.className = 'action-confirm__title';
      title.id = 'action-confirm-title';
      title.textContent = el.getAttribute('data-confirm-title') || 'Confirmer cette action';
      text.className = 'action-confirm__message';
      text.id = 'action-confirm-message';
      text.textContent = message;
      actions.className = 'action-confirm__actions';
      cancel.type = 'button';
      cancel.className = 'btn btn-ghost';
      cancel.textContent = 'Annuler';
      cancel.autofocus = true;
      confirm.type = 'button';
      confirm.className = el.hasAttribute('data-confirm-danger') ? 'btn btn-danger' : 'btn';
      confirm.textContent = el.getAttribute('data-confirm-label') || 'Confirmer';

      dialog.setAttribute('aria-labelledby', title.id);
      dialog.setAttribute('aria-describedby', text.id);
      content.className = 'action-confirm__content';
      content.appendChild(title);
      content.appendChild(text);
      if (el.tagName === 'FORM' && el.hasAttribute('data-confirm-summary')) {
        summary = document.createElement('ul');
        summary.className = 'action-confirm__summary';
        $$('.input', el).forEach(function (control) {
          if (control.disabled || control.type === 'hidden' || !control.getClientRects().length) { return; }
          var label = control.labels && control.labels.length ? control.labels[0].textContent : control.name;
          var value = control.value;
          if (control.tagName === 'SELECT') {
            value = control.options[control.selectedIndex] ? control.options[control.selectedIndex].textContent : '';
          } else if (control.type === 'checkbox') {
            value = control.checked ? 'Oui' : 'Non';
          }
          label = (label || control.name).replace(/\s+/g, ' ').trim();
          value = (value || '').replace(/\s+/g, ' ').trim() || '(non renseigné)';
          var item = document.createElement('li');
          var name = document.createElement('strong');
          var fieldValue = document.createElement('span');
          name.textContent = label;
          fieldValue.textContent = value;
          item.appendChild(name);
          item.appendChild(fieldValue);
          summary.appendChild(item);
        });
        content.appendChild(summary);
      }
      actions.appendChild(cancel);
      actions.appendChild(confirm);
      content.appendChild(actions);
      dialog.appendChild(content);
      document.body.appendChild(dialog);

      function finish(accepted) {
        if (settled) { return; }
        settled = true;
        confirmationOpen = false;
        if (dialog.open) { dialog.close(); }
        dialog.remove();
        if (previousFocus && previousFocus.isConnected) { previousFocus.focus(); }
        resolve(accepted);
      }
      cancel.addEventListener('click', function () { finish(false); });
      confirm.addEventListener('click', function () { finish(true); });
      dialog.addEventListener('cancel', function (event) { event.preventDefault(); finish(false); });
      dialog.addEventListener('click', function (event) { if (event.target === dialog) { finish(false); } });
      dialog.showModal();
      cancel.focus();
    });
  }

  $$('[data-confirm]').forEach(function (el) {
    if (el.tagName === 'FORM') {
      el.addEventListener('submit', function (event) {
        if (el.dataset.confirmed === 'true') { delete el.dataset.confirmed; return; }
        event.preventDefault();
        var submitter = event.submitter;
        askConfirmation(el).then(function (accepted) {
          if (!accepted) { return; }
          el.dataset.confirmed = 'true';
          if (submitter && el.requestSubmit) { el.requestSubmit(submitter); }
          else if (el.requestSubmit) { el.requestSubmit(); }
          else { el.submit(); }
        });
      });
    } else {
      el.addEventListener('click', function (event) {
        event.preventDefault();
        askConfirmation(el).then(function (accepted) {
          if (!accepted) { return; }
          if (el.href) { window.location.assign(el.href); }
          else if (el.form && el.form.requestSubmit) { el.form.requestSubmit(el); }
        });
      });
    }
  });

  /* ---- Visionneuse d'images ---- */
  var lb = $('#lightbox');
  if (lb) {
    var lbImg = $('img', lb);
    $$('[data-zoom]').forEach(function (p) {
      var openIt = function () { lbImg.src = p.getAttribute('data-zoom'); lbImg.alt = p.getAttribute('data-alt') || ''; lb.classList.add('open'); };
      p.addEventListener('click', openIt);
      p.addEventListener('keydown', function (e) { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); openIt(); } });
    });
    lb.addEventListener('click', function () { lb.classList.remove('open'); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') lb.classList.remove('open'); });
  }


  /* ---- Confettis de remerciement ---- */
  var cf = $('[data-confetti]');
  if (cf && !reduce) {
    var c = document.createElement('canvas');
    c.style.cssText = 'position:fixed;inset:0;width:100%;height:100%;pointer-events:none;z-index:150';
    document.body.appendChild(c);
    var g = c.getContext('2d'); c.width = window.innerWidth; c.height = window.innerHeight;
    var cols = ['#f6a623', '#3498db', '#1fa971', '#e5566d', '#ffffff'], bits = [];
    for (var k = 0; k < 140; k++) bits.push({ x: c.width / 2 + (Math.random() - .5) * 200, y: c.height * .35, vx: (Math.random() - .5) * 13, vy: -Math.random() * 13 - 4,
      s: 5 + Math.random() * 7, r: Math.random() * 6, vr: (Math.random() - .5) * .4, col: cols[k % cols.length] });
    var t0 = performance.now();
    (function fall(now) {
      g.clearRect(0, 0, c.width, c.height);
      bits.forEach(function (p) { p.vy += .32; p.x += p.vx; p.y += p.vy; p.vx *= .99; p.r += p.vr;
        g.save(); g.translate(p.x, p.y); g.rotate(p.r); g.fillStyle = p.col; g.fillRect(-p.s / 2, -p.s / 3, p.s, p.s * .6); g.restore(); });
      if (now - t0 < 3600) requestAnimationFrame(fall); else c.remove();
    })(t0);
  }

  /* ---- Vidéos : lecture seulement quand elles sont visibles ---- */
  var vids = $$('video[data-autoplay]');
  if (vids.length && 'IntersectionObserver' in window) {
    var vio = new IntersectionObserver(function (entries) {
      entries.forEach(function (en) {
        if (en.isIntersecting) { var p = en.target.play(); if (p && p.catch) p.catch(function () {}); } else { en.target.pause(); }
      });
    }, { threshold: 0.35 });
    vids.forEach(function (v) { vio.observe(v); });
  }
})();
