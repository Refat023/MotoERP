<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();
$request = Illuminate\Http\Request::create('/sales/search-product', 'GET', ['q' => 'TEST-001']);
$response = $app->handle($request);
echo $response->getStatusCode() . PHP_EOL;
echo $response->getContent() . PHP_EOL;
