<?php

declare(strict_types=1);

require_once __DIR__ . '/src/helpers.php';
require_once __DIR__ . '/src/Card.php';
require_once __DIR__ . '/src/CardGenerator.php';
require_once __DIR__ . '/src/CardStorage.php';
require_once __DIR__ . '/src/Validator.php';
require_once __DIR__ . '/src/PdfGenerator.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: pages/formulario-originales.html');
    exit;
}

try {
    $config = Validator::originales($_POST);

    $generator = new CardGenerator();
    $cards     = $generator->generateList($config['cantidad']);

    CardStorage::save($cards, __DIR__ . '/txt/originales.txt');

    (new PdfGenerator())->renderOriginales($cards, $config);

} catch (InvalidArgumentException $e) {
    renderError($e->getMessage(), 'pages/formulario-originales.html');
} catch (Throwable $e) {
    renderError('Error inesperado: ' . $e->getMessage(), 'pages/formulario-originales.html');
}
