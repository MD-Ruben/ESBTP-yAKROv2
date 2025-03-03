#!/usr/bin/env php
<?php

// Autoloader de Composer
require __DIR__ . '/vendor/autoload.php';

// Charger l'application Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\DB;

echo "=== Script de réparation des permissions ===\n";

// Réinitialiser les caches des permissions
app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
echo "✅ Cache des permissions réinitialisé.\n\n";

// Fonction pour s'assurer qu'une permission existe
function ensurePermissionExists($permissionName) {
    $permission = Permission::where('name', $permissionName)->first();
    if (!$permission) {
        $permission = Permission::create(['name' => $permissionName]);
        echo "✅ Permission '{$permissionName}' créée.\n";
    } else {
        echo "ℹ️ Permission '{$permissionName}' existe déjà.\n";
    }
    return $permission;
}

// Liste des permissions d'attendance
$attendancePermissions = [
    'create attendance',
    'view attendances',
    'edit attendances',
    'delete attendances'
];

// Vérifier et créer les permissions manquantes
echo "Vérification des permissions d'attendance...\n";
foreach ($attendancePermissions as $permissionName) {
    ensurePermissionExists($permissionName);
}

// Vérifier et créer les permissions d'inscription si elles n'existent pas déjà
$inscriptionPermissions = [
    'inscriptions.view',
    'inscriptions.create',
    'inscriptions.edit',
    'inscriptions.delete',
    'inscriptions.validate'
];

echo "Vérification et création des permissions d'inscription...\n";
foreach ($inscriptionPermissions as $permissionName) {
    $permission = Permission::firstOrCreate(['name' => $permissionName]);
    echo "Permission '{$permissionName}' créée ou confirmée.\n";
}

// Récupérer ou créer le rôle superAdmin
$superAdmin = Role::where('name', 'superAdmin')->first();
if (!$superAdmin) {
    $superAdmin = Role::create(['name' => 'superAdmin']);
    echo "✅ Rôle 'superAdmin' créé.\n";
} else {
    echo "ℹ️ Rôle 'superAdmin' existe déjà.\n";
}

// Attribuer les permissions au rôle superAdmin
echo "\nAttribution des permissions au rôle superAdmin...\n";
foreach ($attendancePermissions as $permissionName) {
    if (!$superAdmin->hasPermissionTo($permissionName)) {
        $superAdmin->givePermissionTo($permissionName);
        echo "✅ Permission '{$permissionName}' attribuée au superAdmin.\n";
    } else {
        echo "ℹ️ superAdmin a déjà la permission '{$permissionName}'.\n";
    }
}

// Attribuer toutes les permissions au rôle superAdmin
echo "\nAttribution de toutes les permissions au rôle superAdmin...\n";
foreach ($inscriptionPermissions as $permissionName) {
    if (!$superAdmin->hasPermissionTo($permissionName)) {
        $superAdmin->givePermissionTo($permissionName);
        echo "✅ Permission '{$permissionName}' attribuée au superAdmin.\n";
    } else {
        echo "ℹ️ superAdmin a déjà la permission '{$permissionName}'.\n";
    }
}

// Vérifier les utilisateurs avec le rôle superAdmin
echo "\nUtilisateurs avec le rôle superAdmin :\n";
$users = User::role('superAdmin')->get();
if ($users->count() > 0) {
    foreach ($users as $user) {
        echo "- {$user->name} ({$user->email})\n";
    }
} else {
    echo "⚠️ Aucun utilisateur n'a le rôle superAdmin.\n";
}

// Vérifier les permissions attribuées au rôle superAdmin
echo "\nPermissions attribuées au rôle superAdmin :\n";
$permissions = $superAdmin->permissions;
foreach ($permissions as $permission) {
    echo "- {$permission->name}\n";
}

echo "\nCommandes pour nettoyer les caches :\n";
echo "php artisan config:clear\n";
echo "php artisan view:clear\n";
echo "php artisan permission:cache-reset\n";

echo "\n=== Fin du script ===\n"; 