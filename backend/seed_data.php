<?php
/**
 * Data seeding script - Populates database with real SEAL Team data
 * Run once from backend directory: php seed_data.php
 */

// Database connection
$dsn = 'mysql:host=mysql;dbname=USNAVY;charset=utf8mb4';
$user = 'armory_user';
$pass = 'armory_password';

try {
    $db = new PDO($dsn, $user, $pass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "[✓] Connected to database\n";
} catch (PDOException $e) {
    die("[✗] Database connection failed: " . $e->getMessage());
}

// Read seal_team_data.json
$json_path = '/app/seal_team_data.json';
if (!file_exists($json_path)) {
    die("[✗] seal_team_data.json not found at: $json_path\n");
}

$data = json_decode(file_get_contents($json_path), true);
if (!$data) {
    die("[✗] Failed to parse JSON\n");
}

echo "[✓] Loaded seal_team_data.json\n";

// Clear existing data (except users)
$db->exec("SET FOREIGN_KEY_CHECKS = 0");
$db->exec("DELETE FROM dotazioni_personale");
$db->exec("DELETE FROM personale");
$db->exec("DELETE FROM squadre");
$db->exec("DELETE FROM equipaggiamento");
$db->exec("DELETE FROM categorie_equipaggiamento");
$db->exec("SET FOREIGN_KEY_CHECKS = 1");
echo "[✓] Cleared existing data\n";

// Reset AUTO_INCREMENT
$db->exec("ALTER TABLE squadre AUTO_INCREMENT = 1");
$db->exec("ALTER TABLE equipaggiamento AUTO_INCREMENT = 1");
$db->exec("ALTER TABLE categorie_equipaggiamento AUTO_INCREMENT = 1");

// 1. Insert equipment categories
$categories = [
    ['nome' => 'Fucili d\'Assalto', 'desc' => 'Fucili da assalto e carabine tattiche'],
    ['nome' => 'Pistole', 'desc' => 'Pistole da combattimento'],
    ['nome' => 'Precisione', 'desc' => 'Fucili di precisione e sniper'],
    ['nome' => 'Supporto Pesante', 'desc' => 'Mitragliatrici e armi di supporto'],
    ['nome' => 'Accessori/K9', 'desc' => 'Accessori ottici, silenziatori, cani da combattimento'],
];

$cat_stmt = $db->prepare("INSERT INTO categorie_equipaggiamento (nome_categoria, descrizione) VALUES (?, ?)");
foreach ($categories as $cat) {
    $cat_stmt->execute([$cat['nome'], $cat['desc']]);
}
echo "[✓] Inserted " . count($categories) . " equipment categories\n";

// 2. Insert teams
$teams = [
    'Bravo Team',
    'Alpha Team',
    'Charlie Team',
    'Echo Team',
    'Delta Force',
];

$team_stmt = $db->prepare("INSERT INTO squadre (nome_squadra, stato) VALUES (?, 'Disponibile')");
$team_ids = [];
foreach ($teams as $team) {
    $team_stmt->execute([$team]);
    $team_ids[$team] = $db->lastInsertId();
}
echo "[✓] Inserted " . count($teams) . " teams\n";

// 3. Map characters to teams
$character_mapping = [
    'Clay Spenser' => ['team' => 'Bravo Team', 'grado' => 'Petty Officer First Class', 'sesso' => 'Maschio'],
    'Percival Sonny Quinn' => ['team' => 'Bravo Team', 'grado' => 'Chief Petty Officer', 'sesso' => 'Maschio'],
    'Lisa Davis' => ['team' => 'Bravo Team', 'grado' => 'Lieutenant', 'sesso' => 'Femmina'],
    'Jason Hayes' => ['team' => 'Bravo Team', 'grado' => 'Master Chief Petty Officer', 'sesso' => 'Maschio'],
    'Michael Chen' => ['team' => 'Bravo Team', 'grado' => 'Petty Officer', 'sesso' => 'Maschio'],
    'Omar Hamza' => ['team' => 'Bravo Team', 'grado' => 'Petty Officer', 'sesso' => 'Maschio'],
    'Wes Soto' => ['team' => 'Bravo Team', 'grado' => 'Petty Officer', 'sesso' => 'Maschio'],
    'Mandy Ellis' => ['team' => 'Bravo Team', 'grado' => 'Lieutenant Commander', 'sesso' => 'Femmina'],
];

// 5. Insert personnel with proper schema
$pers_stmt = $db->prepare(
    "INSERT INTO personale (personale_id, nome, cognome, grado, squadra_id, stato_servizio, sesso) 
     VALUES (?, ?, ?, ?, ?, ?, ?)"
);

$personale_ids = [];
foreach ($data['characters'] as $idx => $char) {
    $real_name = $char['real_name'];
    
    if (!isset($character_mapping[$real_name])) {
        continue;
    }
    
    $name_parts = explode(' ', $real_name);
    $first_name = array_shift($name_parts);
    $last_name = implode(' ', $name_parts);
    
    $mapping = $character_mapping[$real_name];
    $team_id = $team_ids[$mapping['team']] ?? $team_ids['Bravo Team'];
    $status = ($char['status'] ?? '') === 'Deceased' ? 'Deceduto' : 'Attivo';
    $personale_id = 'SEAL_' . str_pad($idx + 1, 3, '0', STR_PAD_LEFT);
    
    $pers_stmt->execute([
        $personale_id,
        $first_name,
        $last_name,
        $mapping['grado'],
        $team_id,
        $status,
        $mapping['sesso']
    ]);
    
    $personale_ids[$real_name] = $personale_id;
}
echo "[✓] Inserted " . count($personale_ids) . " personnel\n";

// 6. Insert equipment
$equipment_data = [
    ['model' => 'HK416', 'serial' => 'HK416-001', 'cat' => 'Fucili d\'Assalto'],
    ['model' => 'M4A1 Carbine', 'serial' => 'M4A1-001', 'cat' => 'Fucili d\'Assalto'],
    ['model' => 'Mk18 CQBR', 'serial' => 'MK18-001', 'cat' => 'Fucili d\'Assalto'],
    ['model' => 'Sig Sauer P226', 'serial' => 'P226-001', 'cat' => 'Pistole'],
    ['model' => 'Glock 19', 'serial' => 'G19-001', 'cat' => 'Pistole'],
    ['model' => 'M67 Frag', 'serial' => 'M67-001', 'cat' => 'Supporto Pesante'],
    ['model' => 'M84 Stun', 'serial' => 'M84-001', 'cat' => 'Supporto Pesante'],
    ['model' => 'FAST Helmet', 'serial' => 'FAST-001', 'cat' => 'Accessori/K9'],
    ['model' => 'Body Armor', 'serial' => 'BAPC-001', 'cat' => 'Accessori/K9'],
    ['model' => 'Night Vision (PVS-14)', 'serial' => 'NVG-001', 'cat' => 'Accessori/K9'],
    ['model' => 'PRC-152 Radio', 'serial' => 'PRC152-001', 'cat' => 'Accessori/K9'],
    ['model' => 'Tactical Headset', 'serial' => 'THS-001', 'cat' => 'Accessori/K9'],
];

// Get category IDs
$cat_ids = [];
$cat_result = $db->query("SELECT categoria_id, nome_categoria FROM categorie_equipaggiamento");
while ($row = $cat_result->fetch(PDO::FETCH_ASSOC)) {
    $cat_ids[$row['nome_categoria']] = $row['categoria_id'];
}

$equip_stmt = $db->prepare(
    "INSERT INTO equipaggiamento (nome_modello, numero_seriale, categoria_id, stato) 
     VALUES (?, ?, ?, 'Disponibile')"
);

foreach ($equipment_data as $equip) {
    $cat_id = $cat_ids[$equip['cat']] ?? 1;
    $equip_stmt->execute([
        $equip['model'],
        $equip['serial'],
        $cat_id
    ]);
}
echo "[✓] Inserted " . count($equipment_data) . " equipment items\n";

echo "\n[✓] Data seeding completed successfully!\n";
echo "Database is now populated with SEAL Team data.\n";
