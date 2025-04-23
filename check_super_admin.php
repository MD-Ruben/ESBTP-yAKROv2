<?php

// Initialiser l'application Laravel
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Importer les classes
use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "=== RECHERCHE DE COMPTES SUPER ADMIN ===\n\n";

// Rechercher les utilisateurs avec le rôle superAdmin
$superAdmins = User::where('role', 'superAdmin')->get();

if ($superAdmins->isEmpty()) {
    echo "Aucun utilisateur avec le rôle 'superAdmin' n'a été trouvé.\n\n";
    
    // Rechercher l'utilisateur Ruben
    $ruben = User::where('email', 'ruben@gmail.com')->first();
    
    if ($ruben) {
        echo "L'utilisateur Ruben a été trouvé avec les détails suivants:\n";
        echo "ID: {$ruben->id}\n";
        echo "Nom: {$ruben->name}\n";
        echo "Email: {$ruben->email}\n";
        echo "Rôle actuel: " . ($ruben->role ?? 'Non défini') . "\n\n";
    } else {
        echo "L'utilisateur avec l'email 'ruben@gmail.com' n'a pas été trouvé.\n\n";
    }
    
    // Rechercher d'autres utilisateurs administrateurs
    echo "Autres utilisateurs avec des rôles administratifs:\n";
    $admins = User::whereIn('role', ['admin', 'secretaire'])
                  ->orWhere('name', 'like', '%admin%')
                  ->orWhere('email', 'like', '%admin%')
                  ->get();
    
    if ($admins->isEmpty()) {
        echo "Aucun autre utilisateur administrateur trouvé.\n";
    } else {
        foreach ($admins as $index => $admin) {
            echo "#{$index}: {$admin->name} ({$admin->email}) - Rôle: " . ($admin->role ?? 'Non défini') . "\n";
        }
    }
} else {
    echo "Comptes super admin trouvés (" . $superAdmins->count() . "):\n\n";
    
    foreach ($superAdmins as $index => $admin) {
        echo "Super Admin #{$index + 1}\n";
        echo "ID: {$admin->id}\n";
        echo "Nom: {$admin->name}\n";
        echo "Email: {$admin->email}\n";
        echo "Rôle: {$admin->role}\n";
        echo "Créé le: {$admin->created_at}\n\n";
    }
}

echo "=== FIN DE LA RECHERCHE ===\n"; 