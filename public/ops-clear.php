<?php

use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Kernel::class);

$token = $_GET['token'] ?? '';
$expectedToken = 'CAMBIA_ESTE_TOKEN_TEMPORAL_POR_UNO_LARGO_Y_UNICO';

if (! is_string($token) || ! hash_equals($expectedToken, $token)) {
    http_response_code(403);
    header('Content-Type: text/plain; charset=UTF-8');
    exit('Forbidden');
}

$kernel->call('optimize:clear');
$kernel->call('config:clear');
$kernel->call('cache:clear');
$kernel->call('route:clear');
$kernel->call('view:clear');

header('Content-Type: text/plain; charset=UTF-8');
echo 'Caches limpiadas correctamente.';
