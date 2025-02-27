<?php

/**
 * Script pour rechercher et corriger les liens codés en dur dans les vues
 * 
 * Ce script parcourt tous les fichiers de vue (.blade.php) et remplace les liens
 * codés en dur par des fonctions route() ou url() de Laravel.
 */

// Charger l'application Laravel
require __DIR__ . '/../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Afficher l'en-tête
echo "\n";
echo "=================================================================\n";
echo "      CORRECTION DES LIENS CODÉS EN DUR DANS LES VUES           \n";
echo "=================================================================\n\n";

// Définir le répertoire des vues
$viewsDir = __DIR__ . '/../../resources/views';

// Liste des liens codés en dur à rechercher et remplacer
$hardcodedLinks = [
    'href="/login"' => 'href="{{ route(\'login\') }}"',
    'href="/register"' => 'href="{{ route(\'register\') }}"',
    'href="/dashboard"' => 'href="{{ route(\'dashboard\') }}"',
    'href="/home"' => 'href="{{ route(\'home\') }}"',
    'href="/logout"' => 'href="{{ route(\'logout\') }}"',
    'href="/password/reset"' => 'href="{{ route(\'password.request\') }}"',
    'href="/password/email"' => 'href="{{ route(\'password.email\') }}"',
    'href="/password/reset/"' => 'href="{{ route(\'password.reset\', $token) }}"',
    'href="/email/verify"' => 'href="{{ route(\'verification.notice\') }}"',
    'href="/email/resend"' => 'href="{{ route(\'verification.resend\') }}"',
    'action="/login"' => 'action="{{ route(\'login\') }}"',
    'action="/register"' => 'action="{{ route(\'register\') }}"',
    'action="/logout"' => 'action="{{ route(\'logout\') }}"',
    'action="/password/email"' => 'action="{{ route(\'password.email\') }}"',
    'action="/password/reset"' => 'action="{{ route(\'password.update\') }}"',
];

// Fonction pour parcourir récursivement un répertoire
function scanDirectory($dir, $hardcodedLinks) {
    $files = scandir($dir);
    $modifiedFiles = 0;
    
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }
        
        $path = $dir . '/' . $file;
        
        if (is_dir($path)) {
            // Parcourir récursivement les sous-répertoires
            $modifiedFiles += scanDirectory($path, $hardcodedLinks);
        } else if (pathinfo($path, PATHINFO_EXTENSION) === 'php') {
            // Vérifier si c'est un fichier de vue Blade
            if (strpos($file, '.blade.php') !== false || strpos(file_get_contents($path), '@extends') !== false) {
                $content = file_get_contents($path);
                $originalContent = $content;
                $modified = false;
                
                // Rechercher et remplacer les liens codés en dur
                foreach ($hardcodedLinks as $search => $replace) {
                    if (strpos($content, $search) !== false) {
                        $content = str_replace($search, $replace, $content);
                        $modified = true;
                    }
                }
                
                // Si des modifications ont été apportées, enregistrer le fichier
                if ($modified) {
                    file_put_contents($path, $content);
                    echo "✓ Liens corrigés dans " . str_replace(__DIR__ . '/../../', '', $path) . "\n";
                    $modifiedFiles++;
                }
            }
        }
    }
    
    return $modifiedFiles;
}

// Parcourir le répertoire des vues
echo "Recherche de liens codés en dur dans les vues...\n\n";
$modifiedFiles = scanDirectory($viewsDir, $hardcodedLinks);

// Afficher le résultat
echo "\n=================================================================\n";
echo "                        RÉSULTAT                                 \n";
echo "=================================================================\n\n";

if ($modifiedFiles > 0) {
    echo "✓ " . $modifiedFiles . " fichier(s) ont été modifiés.\n";
    echo "Les liens codés en dur ont été remplacés par des fonctions route() ou url().\n";
} else {
    echo "Aucun lien codé en dur n'a été trouvé dans les vues.\n";
}

echo "\nVeuillez redémarrer votre serveur web pour que les modifications prennent effet.\n";

echo "\n=================================================================\n";
echo "                        FIN                                      \n";
echo "=================================================================\n\n"; 