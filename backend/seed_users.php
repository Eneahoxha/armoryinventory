<?php
/**
 * Seed users into the database
 * Run: php backend/seed_users.php
 */

require_once __DIR__ . '/api/config/Database.php';
require_once __DIR__ . '/api/models/User.php';

try {
    $database = new Database();
    $db = $database->connect();
    
    echo "Connected to database!\n";
    
    // Create users table
    $user = new User($db);
    $user->createTable();
    echo "✓ Users table ready\n\n";
    
    // Define seed users (from users.local.json)
    $seedUsers = [
        [
            'email' => 'admin@armory.local',
            'password' => 'admin123456',
            'nome' => 'Admin',
            'cognome' => 'System',
            'ruolo' => 'admin'
        ],
        [
            'email' => 'caposm@armory.local',
            'password' => 'capo123456',
            'nome' => 'Jason',
            'cognome' => 'Hayes',
            'ruolo' => 'capo_sm'
        ],
        [
            'email' => 'armaiolo@armory.local',
            'password' => 'armaiolo123456',
            'nome' => 'Raymond',
            'cognome' => 'Perry',
            'ruolo' => 'armaiolo'
        ],
        [
            'email' => 'ufficiale@armory.local',
            'password' => 'ufficiale123456',
            'nome' => 'Sonny',
            'cognome' => 'Quinn',
            'ruolo' => 'ufficiale'
        ],
        [
            'email' => 'operatore@armory.local',
            'password' => 'operatore123456',
            'nome' => 'Clay',
            'cognome' => 'Spenser',
            'ruolo' => 'operatore'
        ]
    ];
    
    // Insert users
    foreach ($seedUsers as $userData) {
        try {
            $result = $user->create(
                $userData['email'],
                $userData['password'],
                $userData['nome'],
                $userData['cognome'],
                $userData['ruolo']
            );
            echo "✓ Created user: {$userData['email']} ({$userData['ruolo']})\n";
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'already registered') !== false) {
                echo "⚠ User already exists: {$userData['email']}\n";
            } else {
                throw $e;
            }
        }
    }
    
    echo "\n✅ Users seeding complete!\n";
    echo "\nAvailable credentials for testing:\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    foreach ($seedUsers as $user) {
        echo "📧 {$user['email']}\n";
        echo "   🔐 {$user['password']}\n";
        echo "   👤 Role: {$user['ruolo']}\n\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
