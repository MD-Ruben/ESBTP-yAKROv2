<?php
require_once 'vendor/autoload.php';
require_once 'bootstrap/app.php';

$app = app();
$db = $app->make('db');

// Marquer la migration comme terminée
$db->table('migrations')->insert([
    'migration' => '2025_02_27_001419_add_role_and_is_active_to_users_table',
    'batch' => 2
]);

echo "Migration marked as completed\n";
?>
