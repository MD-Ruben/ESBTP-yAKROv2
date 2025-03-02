<?php

// Initialiser l'application Laravel
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

// Liste des permissions à ajouter
$permissions = [
    'inscriptions.view',
    'inscriptions.create',
    'inscriptions.edit',
    'inscriptions.delete',
    'inscriptions.validate'
];

// Trouver le rôle superAdmin
$role = Role::findByName('superAdmin');

if (!$role) {
    echo "Rôle superAdmin non trouvé.\n";
    exit(1);
}

// Créer et ajouter les permissions
foreach ($permissions as $permissionName) {
    $permission = Permission::firstOrCreate(['name' => $permissionName]);
    if (!$role->hasPermissionTo($permissionName)) {
        $role->givePermissionTo($permissionName);
        echo "Permission '{$permissionName}' ajoutée au rôle superAdmin.\n";
    } else {
        echo "Le rôle superAdmin a déjà la permission '{$permissionName}'.\n";
    }
}

echo "Toutes les permissions d'inscription ont été ajoutées au rôle superAdmin.\n"; 