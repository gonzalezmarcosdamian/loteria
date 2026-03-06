<?php

declare(strict_types=1);

/**
 * Valida y sanitiza los datos del formulario antes de procesarlos.
 * Lanza InvalidArgumentException si algún campo es inválido.
 */
final class Validator
{
    /**
     * Valida los datos del formulario de cartones originales.
     *
     * @throws InvalidArgumentException
     */
    public static function originales(array $post): array
    {
        return [
            'cantidad'    => self::positiveInt($post, 'cantidaddecartones', 'Cantidad de cartones'),
            'organiza'    => self::string($post, 'Organiza', 'Organiza'),
            'fecha'       => self::string($post, 'Fecha', 'Fecha'),
            'hora'        => self::string($post, 'Hora', 'Hora'),
            'lugar'       => self::string($post, 'Lugar', 'Lugar'),
            'costocarton' => self::string($post, 'costocarton', 'Costo del cartón'),
            'premios'     => self::premios($post),
        ];
    }

    /**
     * Valida los datos del formulario de cartones adicionales/sorpresas.
     *
     * @throws InvalidArgumentException
     */
    public static function adicionales(array $post): array
    {
        return [
            'cantidad' => self::positiveInt($post, 'cantidad', 'Cantidad de cartones'),
            'tipo'     => self::string($post, 'tipo', 'Tipo de cartón'),
            'costo'    => self::string($post, 'costo', 'Costo'),
            'fecha'    => self::string($post, 'Fecha', 'Fecha'),
            'hora'     => self::string($post, 'Hora', 'Hora'),
        ];
    }

    /**
     * Valida los datos del formulario de cartones radiales.
     *
     * @throws InvalidArgumentException
     */
    public static function radio(array $post): array
    {
        return [
            'cantidad'    => self::positiveInt($post, 'cantidaddecartones', 'Cantidad de cartones'),
            'organiza'    => self::string($post, 'Organiza', 'Organiza'),
            'fecha'       => self::string($post, 'Fecha', 'Fecha'),
            'hora'        => self::string($post, 'Hora', 'Hora'),
            'costocarton' => self::string($post, 'costocarton', 'Costo del cartón'),
            'contacto'    => self::string($post, 'contacto', 'Contacto'),
            'radio'       => self::string($post, 'radio', 'Radio'),
            'premios'     => self::premios($post),
        ];
    }

    // ─── Privados ────────────────────────────────────────────────────────────

    private static function string(array $post, string $key, string $label): string
    {
        $value = trim($post[$key] ?? '');
        if ($value === '') {
            throw new InvalidArgumentException("El campo \"{$label}\" es obligatorio.");
        }
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    private static function positiveInt(array $post, string $key, string $label): int
    {
        $raw = trim($post[$key] ?? '');
        if (!ctype_digit($raw) || (int) $raw <= 0) {
            throw new InvalidArgumentException(
                "El campo \"{$label}\" debe ser un número entero positivo (recibido: \"{$raw}\")."
            );
        }
        return (int) $raw;
    }

    private static function premios(array $post): array
    {
        $sufijos = ['uno' => 1, 'dos' => 2, 'tres' => 3, 'cuatro' => 4];
        $premios = [];
        foreach ($sufijos as $sufijo => $ronda) {
            $premios[$ronda] = [
                'quintina' => self::string($post, "linia{$sufijo}", "Quintina Ronda {$ronda}"),
                'lleno'    => self::string($post, "lleno{$sufijo}", "Cartón Lleno Ronda {$ronda}"),
            ];
        }
        return $premios;
    }
}
