<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing Error Display Logic...\n";
echo "==============================\n";

// Simulate session with import errors
$importErrors = [
    'Row 1: Email sudah digunakan',
    'Row 2: Travel company tidak ditemukan', 
    'Row 3: Nomor HP sudah digunakan',
    'Row 4: Password terlalu pendek',
    'Row 5: Email format tidak valid'
];

echo "Simulated import errors:\n";
foreach ($importErrors as $error) {
    echo "• " . $error . "\n";
}

echo "\nError count: " . count($importErrors) . "\n";
echo "Button text would be: 'Lihat Detail Error (" . count($importErrors) . " item)'\n";

echo "\nTest completed.\n";
