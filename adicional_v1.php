<?php

declare(strict_types=1);

require_once __DIR__ . '/src/helpers.php';
require_once __DIR__ . '/src/Card.php';
require_once __DIR__ . '/src/CardGenerator.php';
require_once __DIR__ . '/src/CardStorage.php';
require_once __DIR__ . '/src/Validator.php';
require_once __DIR__ . '/src/PdfGenerator.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: pages/formulario-adicionales.html');
    exit;
}

try {
    $config    = Validator::adicionales($_POST);
    $originals = CardStorage::load(__DIR__ . '/txt/originales.txt');

    $generator = new CardGenerator();
    $cards     = $generator->generateAdditionals($config['cantidad'], $originals);

    (new PdfGenerator())->renderAdicionales($cards, $config);

} catch (InvalidArgumentException $e) {
    renderError($e->getMessage(), 'pages/formulario-adicionales.html');
} catch (Throwable $e) {
    renderError('Error inesperado: ' . $e->getMessage(), 'pages/formulario-adicionales.html');
}
