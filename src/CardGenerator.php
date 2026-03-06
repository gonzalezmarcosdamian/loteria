<?php

declare(strict_types=1);

require_once __DIR__ . '/Card.php';

/**
 * Genera cartones de lotería únicos.
 *
 * Correcciones respecto al código original:
 *  - El while de unicidad ahora re-verifica en cada iteración (fix de posible duplicado).
 *  - generar adicionales usa OR en vez de AND para la comparación con originales (fix lógico).
 *  - Los adicionales usan la lista pre-generada en lugar de crear cartones nuevos al imprimir.
 */
final class CardGenerator
{
    /**
     * Genera un cartón aleatorio con 15 números únicos del 1 al 90,
     * garantizando al menos un número por cada decena (1-10, 11-20, ..., 81-90).
     */
    public function generate(): Card
    {
        $numbers = [];

        // Un número garantizado de cada decena para buena distribución
        for ($decade = 0; $decade < 9; $decade++) {
            $numbers[] = random_int($decade * 10 + 1, ($decade + 1) * 10);
        }

        // 6 números adicionales únicos de todo el rango 1-90
        while (count($numbers) < 15) {
            $candidate = random_int(1, 90);
            if (!in_array($candidate, $numbers, strict: true)) {
                $numbers[] = $candidate;
            }
        }

        sort($numbers);

        $grid = [];
        $i = 0;
        for ($row = 0; $row < 3; $row++) {
            for ($col = 0; $col < 5; $col++) {
                $grid[$row][$col] = $numbers[$i++];
            }
        }

        return new Card($grid);
    }

    /**
     * Genera una lista de $count cartones únicos.
     * Dos cartones son duplicados si comparten alguna fila completa.
     *
     * @return Card[]
     */
    public function generateList(int $count): array
    {
        $cards = [];
        while (count($cards) < $count) {
            $candidate = $this->generate();
            if (!$this->isDuplicateIn($candidate, $cards)) {
                $cards[] = $candidate;
            }
        }
        return $cards;
    }

    /**
     * Genera $count cartones que no duplican los originales ni entre sí.
     *
     * @param Card[] $originals
     * @return Card[]
     */
    public function generateAdditionals(int $count, array $originals): array
    {
        $cards = [];
        while (count($cards) < $count) {
            $candidate = $this->generate();
            // Rechazar si está en CUALQUIERA de las dos listas (fix: || en vez de &&)
            if (!$this->isDuplicateIn($candidate, $cards) && !$this->isDuplicateIn($candidate, $originals)) {
                $cards[] = $candidate;
            }
        }
        return $cards;
    }

    /** @param Card[] $list */
    private function isDuplicateIn(Card $candidate, array $list): bool
    {
        foreach ($list as $card) {
            if ($candidate->sharesRowWith($card)) {
                return true;
            }
        }
        return false;
    }
}
