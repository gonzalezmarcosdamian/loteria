<?php

declare(strict_types=1);

/**
 * Objeto de valor inmutable que representa un cartón de lotería: una grilla 3×5
 * con 15 números únicos del 1 al 90, ordenados de menor a mayor.
 */
final class Card
{
    /** @param array<int, array<int, int>> $grid */
    public function __construct(private readonly array $grid) {}

    public function getGrid(): array
    {
        return $this->grid;
    }

    public function getRow(int $row): array
    {
        return $this->grid[$row];
    }

    public function getNumber(int $row, int $col): int
    {
        return $this->grid[$row][$col];
    }

    /**
     * Devuelve true si este cartón comparte alguna fila completa con otro.
     * Se usa para detectar duplicados.
     */
    public function sharesRowWith(self $other): bool
    {
        for ($i = 0; $i < 3; $i++) {
            if ($this->getRow($i) === $other->getRow($i)) {
                return true;
            }
        }
        return false;
    }
}
