<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Http\Request;
use App\Http\Controllers\SaleController;

// Simulate a search request
$request = Request::create('/sales/search-product', 'GET', ['q' => 'TEST-001']);
$controller = new SaleController();
$response = $controller->searchProduct($request);

echo 'Response status: ' . $response->getStatusCode() . PHP_EOL;
echo 'Response content: ' . $response->getContent() . PHP_EOL;