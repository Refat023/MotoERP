<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();
$count = \App\Models\Product::active()->where('sku', 'like', '%TEST-001%')->count();
echo $count . PHP_EOL;
