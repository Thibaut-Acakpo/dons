<?php
require_once __DIR__ . '/includes/functions.php';

$id = (int)get('id', 0);
$st = $pdo->prepare('SELECT d.*, dn.nom, dn.prenom, dn.numero FROM Dons d JOIN Donateur dn ON dn.id = d.id_donateur WHERE d.id_don = ?');
$st->execute(array($id));
$don = $st->fetch();

$donateur = current_donateur();
$autorise = $don && (is_admin()
    || (!empty($_SESSION['dons_ok']) && in_array($id, $_SESSION['dons_ok']))
    || ($donateur && (int)$donateur['id'] === (int)$don['id_donateur']));

if (!$autorise) {
    flash('err', "Reçu introuvable ou accès non autorisé. Identifie-toi avec ton numéro pour consulter tes reçus.");
    redirect('merci.php');
}

$numeroRecu = 'REC-' . date('Y', strtotime($don['date_don'])) . '-' . str_pad((string)$don['id_don'], 5, '0', STR_PAD_LEFT);
$masque = str_repeat('•', max(0, strlen($don['numero']) - 4)) . substr($don['numero'], -4);

if (get('format') === 'pdf') {
  require_once __DIR__ . '/vendor/autoload.php';
  $pdfText = function ($value) {
    $converted = iconv('UTF-8', 'Windows-1252//TRANSLIT//IGNORE', (string)$value);
    return $converted === false ? '' : $converted;
  };
  $pdf = new FPDF('P', 'mm', 'A4');
  $pdf->SetMargins(22, 20, 22);
  $pdf->SetAutoPageBreak(true, 20);
  $pdf->SetTitle($pdfText('Reçu de don ' . $numeroRecu));
  $pdf->SetAuthor($pdfText(SITE_NAME));
  $pdf->AddPage();
  $pdf->SetFillColor(12, 53, 86);
  $pdf->Rect(0, 0, 210, 42, 'F');
  $pdf->SetTextColor(255, 255, 255);
  $pdf->SetXY(22, 14);
  $pdf->SetFont('Arial', 'B', 17);
  $pdf->Cell(166, 10, $pdfText(SITE_NAME), 0, 1, 'C');
  $pdf->SetFont('Arial', '', 10);
  $pdf->Cell(166, 7, $pdfText('Reçu de don'), 0, 1, 'C');

  $pdf->SetTextColor(28, 39, 51);
  $pdf->SetY(54);
  $pdf->SetFont('Arial', 'B', 15);
  $pdf->Cell(166, 10, $pdfText($numeroRecu), 0, 1, 'C');
  $pdf->SetDrawColor(246, 166, 35);
  $pdf->SetLineWidth(1.2);
  $pdf->Line(80, $pdf->GetY(), 130, $pdf->GetY());
  $pdf->Ln(8);

  $rows = array(
    'Donateur' => trim($don['prenom'] . ' ' . $don['nom']),
    'Téléphone' => str_repeat('*', max(0, strlen($don['numero']) - 4)) . substr($don['numero'], -4),
    'Date du don' => date_fr($don['date_don']),
    'Nature du don' => type_label($don['type_don']),
  );
  if ($don['type_don'] === 'Financier') {
    $rows['Montant'] = fcfa($don['montant']);
  } else {
    $rows['Détail'] = (string)$don['detail_don'];
  }

  foreach ($rows as $label => $value) {
    $startY = $pdf->GetY();
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetTextColor(93, 107, 122);
    $pdf->Cell(42, 8, $pdfText($label), 0, 0, 'L');
    $pdf->SetFont('Arial', '', 10);
    $pdf->SetTextColor(28, 39, 51);
    $pdf->MultiCell(124, 8, $pdfText($value !== '' ? $value : '—'), 0, 'L');
    $pdf->SetDrawColor(227, 233, 239);
    $pdf->SetLineWidth(.2);
    $pdf->Line(22, max($startY + 9, $pdf->GetY()), 188, max($startY + 9, $pdf->GetY()));
    $pdf->Ln(3);
  }

  $pdf->Ln(8);
  $pdf->SetFont('Arial', '', 10);
  $pdf->SetTextColor(93, 107, 122);
  $pdf->MultiCell(166, 6, $pdfText('L' . SITE_NAME . ' remercie sincèrement le donateur pour sa générosité envers les enfants accueillis.'), 0, 'C');
  $pdf->Ln(18);
  $pdf->SetTextColor(28, 39, 51);
  $pdf->SetFont('Arial', '', 9);
  $pdf->Cell(80, 7, $pdfText('Fait le ' . date('d/m/Y')), 0, 0, 'L');
  $pdf->Cell(86, 7, $pdfText('Signature et cachet de l’orphelinat'), 0, 1, 'R');
  $pdf->SetDrawColor(93, 107, 122);
  $pdf->Line(125, $pdf->GetY() + 14, 188, $pdf->GetY() + 14);
  $pdf->Output('D', 'recu-' . $numeroRecu . '.pdf');
  exit;
}

$pageTitle = 'Reçu ' . $numeroRecu;
$nav = '';
require __DIR__ . '/includes/header.php';
?>
<div class="wrap">
  <article class="receipt">
    <p class="center muted" style="margin:0"><?= e(SITE_NAME) ?></p>
    <h1>Reçu de don</h1>
    <p class="center"><strong><?= e($numeroRecu) ?></strong></p>
    <dl>
      <dt>Donateur</dt><dd><?= e($don['prenom'] . ' ' . $don['nom']) ?></dd>
      <dt>Téléphone</dt><dd><?= e($masque) ?></dd>
      <dt>Date du don</dt><dd><?= e(date_fr($don['date_don'])) ?></dd>
      <dt>Nature du don</dt><dd><?= type_icon($don['type_don']) ?> <?= e(type_label($don['type_don'])) ?></dd>
      <?php if ($don['type_don'] === 'Financier'): ?>
        <dt>Montant</dt><dd><?= e(fcfa($don['montant'])) ?></dd>
      <?php else: ?>
        <dt>Détail</dt><dd><?= e($don['detail_don']) ?></dd>
      <?php endif; ?>
    </dl>
    <p>L'<?= e(SITE_NAME) ?> remercie sincèrement le donateur pour sa générosité envers les enfants accueillis.</p>
    <div class="sign"><span>Fait le <?= e(date('d/m/Y')) ?></span><span>Signature et cachet de l'orphelinat</span></div>
  </article>
  <p class="center no-print"><a class="btn btn-sun" href="recu.php?id=<?= (int)$don['id_don'] ?>&amp;format=pdf"><?= svg_icon('pdf') ?> Télécharger le PDF</a>
    <button class="btn" type="button" onclick="window.print()"><?= svg_icon('printer') ?> Imprimer le reçu</button>
    <a class="btn btn-ghost" href="merci.php"><?= svg_icon('arrow-left') ?> Retour</a></p>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
