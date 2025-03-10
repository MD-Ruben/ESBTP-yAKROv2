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

// Définir toutes les permissions nécessaires
$allPermissions = [
    // Filières
    'view_filieres', 'create_filieres', 'edit_filieres', 'delete_filieres',

    // Formations
    'view_formations', 'create_formations', 'edit_formations', 'delete_formations',

    // Niveaux d'études
    'view_niveaux_etudes', 'create_niveaux_etudes', 'edit_niveaux_etudes', 'delete_niveaux_etudes',

    // Classes
    'view_classes', 'create_classe', 'edit_classes', 'delete_classes',

    // Étudiants
    'view_students', 'create_student', 'edit_students', 'delete_students',
    'view_own_profile',

    // Examens
    'view_exams', 'create_exam', 'edit_exams', 'delete_exams',
    'view_own_exams',

    // Matières
    'view_matieres', 'create_matieres', 'edit_matieres', 'delete_matieres',

    // Notes
    'view_grades', 'create_grade', 'edit_grades', 'delete_grades',
    'view_own_grades',

    // Bulletins
    'view_bulletins', 'generate_bulletin', 'edit_bulletins', 'delete_bulletins',
    'view_own_bulletin',

    // Emplois du temps
    'view_timetables', 'create_timetable', 'edit_timetables', 'delete_timetables',
    'view_own_timetable',

    // Messages
    'send_messages', 'receive_messages',

    // Présences
    'view_attendances', 'create_attendance', 'edit_attendances', 'delete_attendances',
    'view_own_attendances',

    // Inscriptions
    'inscriptions.view', 'inscriptions.create', 'inscriptions.edit', 'inscriptions.delete', 'inscriptions.validate'
];

// Vérifier et créer toutes les permissions
echo "Vérification et création des permissions...\n";
foreach ($allPermissions as $permissionName) {
    ensurePermissionExists($permissionName);
}

// Définir les permissions pour chaque rôle
$rolePermissions = [
    'superAdmin' => $allPermissions, // Le superAdmin a toutes les permissions

    'secretaire' => [
        // Filières
        'view_filieres',

        // Formations
        'view_formations',

        // Niveaux d'études
        'view_niveaux_etudes',

        // Classes
        'view_classes',

        // Étudiants
        'create_student', 'view_students',

        // Examens
        'view_exams',

        // Matières
        'view_matieres',

        // Notes
        'create_grade', 'view_grades',

        // Bulletins
        'generate_bulletin', 'view_bulletins',

        // Emplois du temps
        'create_timetable', 'view_timetables',

        // Messages
        'send_messages',

        // Présences
        'create_attendance', 'view_attendances',

        // Inscriptions
        'inscriptions.view', 'inscriptions.create', 'inscriptions.edit'
    ],

    'etudiant' => [
        'view_own_profile',
        'view_own_exams',
        'view_own_grades',
        'view_own_bulletin',
        'view_own_timetable',
        'receive_messages',
        'view_own_attendances'
    ]
];

// Récupérer ou créer les rôles
$roles = [];
foreach (array_keys($rolePermissions) as $roleName) {
    $role = Role::where('name', $roleName)->first();
    if (!$role) {
        $role = Role::create(['name' => $roleName]);
        echo "✅ Rôle '{$roleName}' créé.\n";
    } else {
        echo "ℹ️ Rôle '{$roleName}' existe déjà.\n";
    }
    $roles[$roleName] = $role;
}

// Attribuer les permissions aux rôles
echo "\nAttribution des permissions aux rôles...\n";
foreach ($rolePermissions as $roleName => $permissions) {
    $role = $roles[$roleName];
    foreach ($permissions as $permissionName) {
        if (!$role->hasPermissionTo($permissionName)) {
            $role->givePermissionTo($permissionName);
            echo "✅ Permission '{$permissionName}' attribuée au rôle '{$roleName}'.\n";
        } else {
            echo "ℹ️ Le rôle '{$roleName}' a déjà la permission '{$permissionName}'.\n";
        }
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
$permissions = $roles['superAdmin']->permissions;
foreach ($permissions as $permission) {
    echo "- {$permission->name}\n";
}

echo "\nCommandes pour nettoyer les caches :\n";
echo "php artisan config:clear\n";
echo "php artisan view:clear\n";
echo "php artisan permission:cache-reset\n";

echo "\n=== Fin du script ===\n";
