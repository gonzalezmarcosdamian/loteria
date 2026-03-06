<?php

declare(strict_types=1);

/**
 * Muestra una página de error HTML y termina la ejecución.
 * Sólo funciona correctamente si los headers HTTP aún no fueron enviados
 * (es decir, si el error ocurrió antes de generar el PDF).
 */
function renderError(string $message, string $backUrl): never
{
    if (!headers_sent()) {
        http_response_code(400);
    }
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Error — Generador de Cartones</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    </head>
    <body class="bg-light d-flex align-items-center justify-content-center" style="min-height:100vh">
        <div class="card shadow-sm" style="max-width:520px;width:100%">
            <div class="card-body text-center p-5">
                <div class="mb-3" style="font-size:3rem">&#9888;&#65039;</div>
                <h4 class="fw-bold text-danger mb-3">Error al generar los cartones</h4>
                <p class="text-muted"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>
                <a href="<?= htmlspecialchars($backUrl, ENT_QUOTES, 'UTF-8') ?>" class="btn btn-primary mt-3">
                    &larr; Volver al formulario
                </a>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}
