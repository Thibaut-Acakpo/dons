<?php
require_once __DIR__ . '/includes/functions.php';
require_admin();
require_once __DIR__ . '/vendor/autoload.php';

list($where, $params) = dons_filtres();
$st = $pdo->prepare('SELECT d.id_don, d.date_don, dn.nom, dn.prenom, dn.numero, d.type_don, d.montant, d.detail_don
                     FROM Dons d JOIN Donateur dn ON dn.id = d.id_donateur WHERE ' . $where . ' ORDER BY d.date_don DESC, d.id_don DESC');
$st->execute($params);
$dons = $st->fetchAll();

function pdf_dons_text($value) {
    $converted = iconv('UTF-8', 'Windows-1252//TRANSLIT//IGNORE', (string)$value);
    return $converted === false ? '' : $converted;
}

function pdf_dons_wrap($pdf, $text, $width) {
    $text = trim(preg_replace('/\s+/', ' ', $text));
    if ($text === '') { return array('-'); }
    $lines = array();
    $line = '';
    foreach (explode(' ', $text) as $word) {
        $candidate = $line === '' ? $word : $line . ' ' . $word;
        if ($pdf->GetStringWidth($candidate) <= $width) {
            $line = $candidate;
            continue;
        }
        if ($line !== '') { $lines[] = $line; $line = ''; }
        if ($pdf->GetStringWidth($word) <= $width) {
            $line = $word;
            continue;
        }
        $part = '';
        foreach (str_split($word) as $character) {
            if ($part !== '' && $pdf->GetStringWidth($part . $character) > $width) {
                $lines[] = $part;
                $part = '';
            }
            $part .= $character;
        }
        $line = $part;
    }
    if ($line !== '') { $lines[] = $line; }
    return $lines ? $lines : array('-');
}

$pdf = new FPDF('L', 'mm', 'A4');
$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(true, 12);
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 9, pdf_dons_text('Liste des dons'), 0, 1);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(0, 6, pdf_dons_text(count($dons) . ' don(s) - export du ' . date('d/m/Y')), 0, 1);

$filterLabels = array();
if (get('q') !== '') { $filterLabels[] = 'Recherche : ' . get('q'); }
if (in_array(get('type'), types_don(), true)) { $filterLabels[] = 'Type : ' . type_label(get('type')); }
if (valid_date(get('du'))) { $filterLabels[] = 'Du : ' . date_fr(get('du')); }
if (valid_date(get('au'))) { $filterLabels[] = 'Au : ' . date_fr(get('au')); }
$pdf->MultiCell(0, 5, pdf_dons_text($filterLabels ? implode(' | ', $filterLabels) : 'Filtres : aucun'));
$pdf->Ln(2);

$headers = array('N°', 'Date', 'Donateur', 'Téléphone', 'Type', 'Montant', 'Détail');
$widths = array(12, 22, 45, 29, 31, 30, 108);
$drawHeader = function () use ($pdf, $headers, $widths) {
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->SetFillColor(228, 241, 251);
    foreach ($headers as $index => $label) {
        $pdf->Cell($widths[$index], 8, pdf_dons_text($label), 1, $index === count($headers) - 1 ? 1 : 0, 'L', true);
    }
    $pdf->SetFont('Arial', '', 8);
};
$drawHeader();

if (!$dons) {
    $pdf->Cell(array_sum($widths), 8, pdf_dons_text('Aucun don ne correspond à ces filtres.'), 1, 1);
} else {
    foreach ($dons as $don) {
        $amount = $don['type_don'] === 'Financier' ? fcfa($don['montant']) : '-';
        $values = array(
            (string)$don['id_don'],
            date_fr($don['date_don']),
            trim($don['prenom'] . ' ' . $don['nom']),
            (string)$don['numero'],
            type_label($don['type_don']),
            $amount,
            (string)$don['detail_don'],
        );
        $wrapped = array();
        $lineCount = 1;
        foreach ($values as $index => $value) {
            $wrapped[$index] = pdf_dons_wrap($pdf, pdf_dons_text($value), $widths[$index] - 2.5);
            $lineCount = max($lineCount, count($wrapped[$index]));
        }
        $rowHeight = max(7, $lineCount * 4.2 + 2);
        if ($pdf->GetY() + $rowHeight > 197) {
            $pdf->AddPage();
            $drawHeader();
        }
        $rowY = $pdf->GetY();
        $x = 10;
        foreach ($widths as $index => $width) {
            $pdf->Rect($x, $rowY, $width, $rowHeight);
            $pdf->SetXY($x + 1.25, $rowY + 1);
            $pdf->MultiCell($width - 2.5, 4.2, implode("\n", $wrapped[$index]), 0, 'L');
            $x += $width;
        }
        $pdf->SetXY(10, $rowY + $rowHeight);
    }
}

$pdf->Output('D', 'dons-' . date('Y-m-d') . '.pdf');