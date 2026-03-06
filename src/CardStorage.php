<?php

declare(strict_types=1);

require_once __DIR__ . '/Card.php';

/**
 * Persiste y recupera cartones originales en un archivo de texto plano.
 * El archivo se usa para verificar que los cartones adicionales no sean duplicados.
 */
final class CardStorage
{
    /**
     * Guarda la lista de cartones en el archivo indicado.
     * Si el archivo ya existe, lo sobreescribe.
     *
     * @param Card[] $cards
     */
    public static function save(array $cards, string $filePath): void
    {
        $lines = [];
        foreach ($cards as $i => $card) {
            $nums = [];
            for ($row = 0; $row < 3; $row++) {
                for ($col = 0; $col < 5; $col++) {
                    $nums[] = $card->getNumber($row, $col);
                }
            }
            $lines[] = 'Carton' . ($i + 1) . ' ' . implode(' ', $nums);
        }
        file_put_contents($filePath, implode(PHP_EOL, $lines) . PHP_EOL);
    }

    /**
     * Carga cartones desde el archivo indicado.
     *
     * @return Card[]
     */
    public static function load(string $filePath): array
    {
        if (!file_exists($filePath)) {
            return [];
        }

        $handle = fopen($filePath, 'r');
        if ($handle === false) {
            return [];
        }

        $cards = [];
        while (($data = fgetcsv($handle, 0, ' ')) !== false) {
            $grid = [];
            $idx = 1; // posición 0 es el label "CartonN"
            for ($row = 0; $row < 3; $row++) {
                for ($col = 0; $col < 5; $col++) {
                    $grid[$row][$col] = (int) ($data[$idx++] ?? 0);
                }
            }
            $cards[] = new Card($grid);
        }

        fclose($handle);
        return $cards;
    }
}
