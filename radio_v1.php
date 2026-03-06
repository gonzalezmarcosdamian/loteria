<?php

declare(strict_types=1);

require_once __DIR__ . '/src/helpers.php';
require_once __DIR__ . '/src/Card.php';
require_once __DIR__ . '/src/CardGenerator.php';
require_once __DIR__ . '/src/Validator.php';
require_once __DIR__ . '/src/PdfGenerator.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: pages/formulario-radio.html');
    exit;
}

try {
    $config = Validator::radio($_POST);

    $generator = new CardGenerator();
    $cards     = $generator->generateList($config['cantidad']);

    (new PdfGenerator())->renderRadio($cards, $config);

} catch (InvalidArgumentException $e) {
    renderError($e->getMessage(), 'pages/formulario-radio.html');
} catch (Throwable $e) {
    renderError('Error inesperado: ' . $e->getMessage(), 'pages/formulario-radio.html');
}
