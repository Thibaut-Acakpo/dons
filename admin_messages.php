<?php
require_once __DIR__ . '/includes/functions.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && post('action') === 'supprimer') {
    csrf_verify();
    $pdo->prepare('DELETE FROM encouragement WHERE id_encouragement = ?')
        ->execute(array((int)post('id', 0)));
    flash('ok', 'Message supprimé.');
    redirect('admin_messages.php');
}

$msgs = $pdo->query("
    SELECT e.*, dn.nom, dn.prenom, dn.numero
    FROM encouragement e
    LEFT JOIN Donateur dn ON dn.id = e.id_donateur
    ORDER BY e.date_envoi DESC, e.id_encouragement DESC
")->fetchAll();

$adminTitle = 'Messages reçus';
$adminSub = count($msgs) . ' message(s) depuis la page Contact.';
$adminNav = 'messages';
require __DIR__ . '/includes/admin_header.php';
?>

<style>
/* ==========================================
   MESSAGES - Inspiré de la charte graphique
========================================== */
.messages-list{display:flex;flex-direction:column;gap:18px}
.message-item{background:var(--card);border:1px solid var(--line);border-radius:var(--r-sm);padding:0;overflow:hidden;box-shadow:var(--shadow-s);transition:border-color .2s,box-shadow .2s,transform .2s}
.message-item:hover{border-color:rgba(41,128,185,.25);box-shadow:var(--shadow);transform:translateY(-2px)}

/* En-tête */
.message-header{display:flex;justify-content:space-between;align-items:center;gap:15px;flex-wrap:wrap;padding:18px 22px;border-bottom:1px solid var(--line);background:var(--card)}
.message-contact{display:flex;align-items:center;gap:13px;min-width:0}
.message-avatar{width:45px;height:45px;min-width:45px;display:flex;align-items:center;justify-content:center;border-radius:12px;background:var(--sun-100);color:var(--sun-600);font-size:15px;font-weight:800}
.message-author{font-size:1rem;color:var(--blue-900);font-weight:750;margin-bottom:5px;display:flex;align-items:center;flex-wrap:wrap;gap:5px}
.message-meta{font-size:.78rem;color:var(--mute);display:flex;align-items:center;flex-wrap:wrap;gap:7px}
.message-date{display:inline-flex;align-items:center;gap:6px;padding:7px 11px;border-radius:8px;background:var(--blue-100);color:var(--blue-700);font-size:.76rem;font-weight:700;white-space:nowrap}
.visitor-badge{display:inline-block;background:var(--sun-100);color:var(--sun-600);font-size:.65rem;padding:3px 8px;border-radius:99px;font-weight:800;border:1px solid var(--sun)}
.donor-badge{display:inline-block;background:var(--leaf-100);color:var(--leaf);font-size:.65rem;padding:3px 8px;border-radius:99px;font-weight:800}

/* Téléphone */
.message-phone{display:inline-flex;align-items:center;gap:5px;color:var(--blue-700);text-decoration:none;font-weight:650}
.message-phone:hover{text-decoration:underline}

/* Contenu */
.message-content{padding:20px 22px}
.message-content-label{display:flex;align-items:center;gap:7px;margin-bottom:10px;font-size:.73rem;font-weight:800;text-transform:uppercase;letter-spacing:.5px;color:var(--mute)}
.message-body{font-size:.95rem;color:var(--ink);line-height:1.75;white-space:pre-wrap;overflow-wrap:anywhere;padding:15px 17px;background:var(--blue-50);border:1px solid var(--blue-100);border-radius:10px;margin:0}

/* Actions */
.message-actions{display:flex;gap:9px;flex-wrap:wrap;align-items:center;padding:14px 22px;background:var(--blue-50);border-top:1px solid var(--line)}
.message-actions .btn{display:inline-flex;align-items:center;justify-content:center;gap:7px;white-space:nowrap;font-weight:650;transition:.2s}
.btn-whatsapp{background:#25D366;color:white;border:1px solid #25D366;box-shadow:0 4px 10px rgba(37,211,102,.2)}
.btn-whatsapp:hover{background:#128C7E;color:white;box-shadow:0 6px 14px rgba(37,211,102,.35)}
.btn-call{background:var(--blue-500);color:white;border:1px solid var(--blue-500);box-shadow:0 4px 10px rgba(41,128,185,.2)}
.btn-call:hover{background:var(--blue-700);color:white;box-shadow:0 6px 14px rgba(41,128,185,.35)}
.btn-delete{margin-left:auto;color:var(--rose);border:1px solid transparent}
.btn-delete:hover{background:var(--rose-100);border-color:var(--rose)}

/* Coordonnées indisponibles */
.message-no-contact{color:var(--mute);font-size:.83rem;font-style:italic;display:flex;align-items:center;gap:6px}

/* État vide */
.messages-empty{background:var(--card);border:1px dashed var(--line);border-radius:var(--r-sm);text-align:center;padding:55px 20px}
.messages-empty-icon{width:60px;height:60px;display:flex;align-items:center;justify-content:center;margin:0 auto 15px;border-radius:18px;background:var(--sun-100);color:var(--sun-600)}
.messages-empty h3{margin:0 0 7px;color:var(--ink)}
.messages-empty p{margin:0;color:var(--mute);font-size:.9rem}

/* Responsive (Mobile) */
@media(max-width:600px){
  .donor-badge,.visitor-badge{display:none}
  .message-header{padding:15px;flex-direction:column;align-items:flex-start;gap:10px}
  .message-contact{width:100%}
  .message-date{margin-left:58px;font-size:.7rem;padding:5px 9px}
  .message-content{padding:15px}
  .message-actions{padding:13px 15px;gap:8px;flex-direction:column;align-items:stretch}
  .message-actions a,.message-actions form,.message-actions .btn{width:100%;margin-left:0!important}
  .btn-delete{margin-left:0;justify-content:center}
}
</style>

<section class="panel" style="background:transparent;border:none;box-shadow:none;padding:0;">
  <?php if ($msgs): ?>
    <div class="messages-list">
      <?php foreach ($msgs as $m):
        $estInconnu = empty($m['prenom']) && empty($m['nom']);
        $nomComplet = $estInconnu ? 'Visiteur non identifié' : trim($m['prenom'] . ' ' . $m['nom']);
        $numero = $m['numero'] ?? '';
        $numeroWa = preg_replace('/[^0-9]/', '', $numero);
        if (strlen($numeroWa) > 0 && strlen($numeroWa) <= 10) { $numeroWa = '229' . $numeroWa; }
        $aUnNumero = !$estInconnu && !empty($numero);
        $prenom = $m['prenom'] ?? '';
        $message = $m['message'];
        $initPrenom = !empty($m['prenom']) ? substr($m['prenom'], 0, 1) : '';
        $initNom = !empty($m['nom']) ? substr($m['nom'], 0, 1) : '';
        $initiales = $estInconnu ? '?' : strtoupper($initPrenom . $initNom);
        $date = date('d/m/Y à H:i', strtotime($m['date_envoi']));
        $texteReponse = 'Bonjour ' . $prenom . ', nous avons bien reçu votre message. Merci !';
      ?>
      <article class="message-item">
        <!-- IDENTITÉ ET DATE -->
        <div class="message-header">
          <div class="message-contact">
            <div class="message-avatar"><?= e($initiales) ?></div>
            <div>
              <div class="message-author">
                <?= e($nomComplet) ?>
                <?php if ($estInconnu): ?>
                  <span class="visitor-badge">Visiteur</span>
                <?php else: ?>
                  <span class="donor-badge">Donateur</span>
                <?php endif; ?>
              </div>
              <div class="message-meta">
                <?php if ($aUnNumero): ?>
                  <a href="tel:+<?= e($numeroWa) ?>" class="message-phone">
                    <?= svg_icon('phone') ?> <?= e($numero) ?>
                  </a>
                <?php else: ?>
                  <span>Coordonnées indisponibles</span>
                <?php endif; ?>
                <span class="message-date"><?= svg_icon('clipboard') ?> <?= e($date) ?></span>
              </div>
            </div>
          </div>
        </div>

        <!-- MESSAGE -->
        <div class="message-content">
          <div class="message-content-label"><?= svg_icon('message') ?> Contenu du message</div>
          <p class="message-body"><?= e($message) ?></p>
        </div>

        <!-- ACTIONS -->
        <div class="message-actions">
          <?php if ($aUnNumero && $numeroWa): ?>
            <a href="https://wa.me/<?= e($numeroWa) ?>?text=<?= rawurlencode($texteReponse) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-whatsapp">
              <?= svg_icon('message') ?> WhatsApp
            </a>
            <a href="tel:+<?= e($numeroWa) ?>" class="btn btn-sm btn-call">
              <?= svg_icon('phone') ?> Appeler
            </a>
          <?php else: ?>
            <span class="message-no-contact"><?= svg_icon('alert') ?> Aucune coordonnée disponible.</span>
          <?php endif; ?>

          <form method="post" data-confirm="Supprimer définitivement ce message ?" data-confirm-title="Suppression du message" data-confirm-label="Supprimer" data-confirm-danger>
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="supprimer">
            <input type="hidden" name="id" value="<?= (int)$m['id_encouragement'] ?>">
            <button class="btn btn-ghost btn-sm btn-delete" type="submit">
              <?= svg_icon('trash') ?> Supprimer
            </button>
          </form>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <div class="messages-empty">
      <div class="messages-empty-icon"><?= svg_icon('message') ?></div>
      <h3>Aucun message reçu</h3>
      <p>Les messages envoyés depuis votre page Contact apparaîtront ici.</p>
    </div>
  <?php endif; ?>
</section>

<?php require __DIR__ . '/includes/admin_footer.php'; ?>