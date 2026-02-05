<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

try {
    $user = User::create([
        'name' => 'Raphael Nibishaka',
        'email' => 'raphaelnibishaka@gmail.com',
        'password' => Hash::make('password123'),
    ]);
    
    echo "✅ User registered successfully!\n";
    echo "Email: {$user->email}\n";
    echo "Password: password123\n";
    echo "\nYou can now login at: http://127.0.0.1:8000/api/auth/login\n";
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
