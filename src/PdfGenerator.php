<?php

declare(strict_types=1);

require_once __DIR__ . '/../fpdf.php';
require_once __DIR__ . '/Card.php';

// ─────────────────────────────────────────────────────────────────────────────
// Clase base con utilidades compartidas entre todos los documentos PDF
// ─────────────────────────────────────────────────────────────────────────────

abstract class LoteriaPdfBase extends FPDF
{
    /**
     * Convierte texto UTF-8 al encoding Latin-1 (Windows-1252) que usa FPDF.
     * Sin esta conversión, las tildes y la ñ aparecen como caracteres extraños.
     */
    protected function enc(string $text): string
    {
        return iconv('UTF-8', 'windows-1252//TRANSLIT', $text) ?: $text;
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// Documento PDF para cartones ORIGINALES
// ─────────────────────────────────────────────────────────────────────────────

final class OriginalesPdf extends LoteriaPdfBase
{
    public function __construct(private readonly array $cfg)
    {
        parent::__construct('P', 'mm', 'A4');
    }

    public function Header(): void
    {
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(0, 6, $this->enc('Organiza: ' . $this->cfg['organiza']), 0, 1);
        $this->Cell(0, 6, $this->enc('Fecha: ' . $this->cfg['fecha'] . '   Hora: ' . $this->cfg['hora'] . ' hs'), 0, 1);
        $this->Cell(0, 6, $this->enc('Lugar: ' . $this->cfg['lugar']), 0, 1);
        $this->Cell(0, 6, $this->enc('Costo del carton: $' . $this->cfg['costocarton']), 0, 1);
        $this->Ln(4);
    }

    public function Footer(): void
    {
        $this->SetY(-90);
        $this->SetFont('Arial', 'B', 10);

        foreach ($this->cfg['premios'] as $ronda => $premio) {
            $this->Cell(0, 5, $this->enc("PREMIOS: RONDA {$ronda}"), 0, 1);
            $this->Cell(0, 5, $this->enc('QUINTINA: ' . $premio['quintina']), 0, 1);
            $this->Cell(0, 5, $this->enc('CARTON LLENO: ' . $premio['lleno']), 0, 1);
            $this->Ln(2);
        }

        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 5, $this->enc('NOTA: Se entrega el premio, no su valor monetario.'), 0, 1);
        $this->Cell(0, 5, $this->enc('No somos responsables por perdida del carton.'), 0, 1);
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// Documento PDF para cartones ADICIONALES / SORPRESAS
// ─────────────────────────────────────────────────────────────────────────────

final class AdicionalesPdf extends LoteriaPdfBase
{
    public function __construct()
    {
        parent::__construct('P', 'mm', 'A4');
    }

    public function Header(): void
    {
        $this->Ln(2);
    }

    public function Footer(): void
    {
        $this->SetY(-10);
        $this->SetFont('Arial', '', 8);
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// Documento PDF para cartones RADIALES
// ─────────────────────────────────────────────────────────────────────────────

final class RadioPdf extends LoteriaPdfBase
{
    public function __construct(private readonly array $cfg)
    {
        parent::__construct('P', 'mm', 'A4');
    }

    public function Header(): void
    {
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(0, 6, $this->enc('Organiza: ' . $this->cfg['organiza']), 0, 1);
        $this->Cell(0, 6, $this->enc('Fecha: ' . $this->cfg['fecha'] . '   Hora: ' . $this->cfg['hora'] . ' hs'), 0, 1);
        $this->Cell(0, 6, $this->enc('Costo del carton: $' . $this->cfg['costocarton']), 0, 1);
        $this->Ln(4);
    }

    public function Footer(): void
    {
        $this->SetY(-90);
        $this->SetFont('Arial', 'B', 10);

        foreach ($this->cfg['premios'] as $ronda => $premio) {
            $this->Cell(0, 5, $this->enc("PREMIOS: RONDA {$ronda}"), 0, 1);
            $this->Cell(0, 5, $this->enc('QUINTINA: ' . $premio['quintina']), 0, 1);
            $this->Cell(0, 5, $this->enc('CARTON LLENO: ' . $premio['lleno']), 0, 1);
            $this->Ln(2);
        }

        $this->SetFont('Arial', 'B', 11);
        $this->Cell(0, 5, $this->enc('Comunicarse al: ' . $this->cfg['contacto']), 0, 1);
        $this->Cell(0, 5, $this->enc('Por la: ' . $this->cfg['radio']), 0, 1);
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// PdfGenerator — orquesta la renderización de cartones en PDF
// ─────────────────────────────────────────────────────────────────────────────

final class PdfGenerator
{
    private const ROUNDS      = 4;
    private const CELL_SIZE   = 11; // mm

    /**
     * Genera el PDF de cartones originales y lo envía al navegador.
     *
     * @param Card[] $cards
     */
    public function renderOriginales(array $cards, array $config): void
    {
        $pdf = new OriginalesPdf($config);
        $pdf->AddPage();

        foreach ($cards as $index => $card) {
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(0, 5, $pdf->enc('CARTON NRO: ' . ($index + 1)), 0, 1);
            $pdf->Ln(2);
            $this->renderRounds($pdf, $card, self::ROUNDS, self::CELL_SIZE);
            $pdf->AddPage();
        }

        $pdf->Output();
        exit;
    }

    /**
     * Genera el PDF de cartones adicionales/sorpresas y lo envía al navegador.
     * Cada cartón es único y no duplica ningún original.
     *
     * @param Card[] $cards
     */
    public function renderAdicionales(array $cards, array $config): void
    {
        $pdf = new AdicionalesPdf();
        $pdf->AddPage();

        foreach ($cards as $index => $card) {
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(0, 7, $pdf->enc('Fecha: ' . $config['fecha'] . ' - Hora: ' . $config['hora'] . ' hs'), 0, 1);
            $pdf->Cell(0, 7, $pdf->enc('Carton ' . $config['tipo'] . ' Nro: ' . ($index + 1) . ' - Costo: ' . $config['costo']), 0, 1);
            $this->renderGrid($pdf, $card, 10);
            $pdf->Ln(3);

            if (($index + 1) % 5 === 0) {
                $pdf->AddPage();
            }
        }

        $pdf->Output();
        exit;
    }

    /**
     * Genera el PDF de cartones radiales y lo envía al navegador.
     *
     * @param Card[] $cards
     */
    public function renderRadio(array $cards, array $config): void
    {
        $pdf = new RadioPdf($config);
        $pdf->AddPage();

        foreach ($cards as $index => $card) {
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(0, 5, $pdf->enc('CARTON NRO: ' . ($index + 1)), 0, 1);
            $pdf->Ln(2);
            $this->renderRounds($pdf, $card, self::ROUNDS, self::CELL_SIZE);
            $pdf->AddPage();
        }

        $pdf->Output();
        exit;
    }

    // ─── Métodos privados de renderizado ─────────────────────────────────────

    /**
     * Imprime el mismo cartón $rounds veces, con etiqueta "Ronda N" por cada una.
     */
    private function renderRounds(LoteriaPdfBase $pdf, Card $card, int $rounds, int $cellSize): void
    {
        for ($round = 1; $round <= $rounds; $round++) {
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(0, 8, "Ronda {$round}", 0, 1);
            $this->renderGrid($pdf, $card, $cellSize);
        }
        $pdf->Ln(2);
    }

    /**
     * Dibuja la grilla 3×5 del cartón.
     */
    private function renderGrid(LoteriaPdfBase $pdf, Card $card, int $cellSize): void
    {
        $pdf->SetFont('Arial', 'B', 12);
        for ($row = 0; $row < 3; $row++) {
            for ($col = 0; $col < 5; $col++) {
                $pdf->Cell($cellSize, $cellSize, (string) $card->getNumber($row, $col), 1, 0, 'C');
            }
            $pdf->Ln();
        }
    }
}
