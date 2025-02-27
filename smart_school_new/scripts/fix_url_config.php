<?php

/**
 * Script pour vérifier et corriger les problèmes d'URL dans l'application
 * 
 * Ce script vérifie la configuration des URL dans l'application et propose des solutions
 * pour résoudre les problèmes de redirection.
 */

// Charger l'application Laravel
require __DIR__ . '/../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Afficher l'en-tête
echo "\n";
echo "=================================================================\n";
echo "      VÉRIFICATION DE LA CONFIGURATION DES URL DE L'APPLICATION      \n";
echo "=================================================================\n\n";

// Vérifier le fichier .env
echo "1. Vérification du fichier .env\n";
echo "-----------------------------\n";

$envPath = __DIR__ . '/../../.env';
$envContent = file_get_contents($envPath);

// Extraire APP_URL
preg_match('/APP_URL=(.*)/', $envContent, $matches);
$appUrl = isset($matches[1]) ? trim($matches[1]) : 'Non défini';

echo "APP_URL actuel: " . $appUrl . "\n";

// Déterminer l'URL de base
$baseUrl = 'http://' . ($_SERVER['HTTP_HOST'] ?? 'localhost');
$scriptPath = dirname($_SERVER['SCRIPT_NAME']);
$baseDir = dirname(dirname($scriptPath));

if ($baseDir != '/' && $baseDir != '\\') {
    $suggestedUrl = $baseUrl . $baseDir . '/public';
} else {
    $suggestedUrl = $baseUrl . '/public';
}

echo "URL de base suggérée: " . $suggestedUrl . "\n\n";

// Vérifier le fichier .htaccess à la racine
echo "2. Vérification du fichier .htaccess à la racine\n";
echo "--------------------------------------------\n";

$rootHtaccessPath = __DIR__ . '/../../.htaccess';
if (file_exists($rootHtaccessPath)) {
    echo "Le fichier .htaccess existe à la racine du projet.\n";
    $htaccessContent = file_get_contents($rootHtaccessPath);
    if (strpos($htaccessContent, 'RewriteRule ^(.*)$ public/$1') !== false) {
        echo "✓ Le fichier .htaccess contient une règle de redirection vers le dossier public.\n\n";
    } else {
        echo "✗ Le fichier .htaccess ne contient pas de règle de redirection vers le dossier public.\n";
        echo "  Suggestion: Ajoutez les règles suivantes au fichier .htaccess:\n\n";
        echo "  <IfModule mod_rewrite.c>\n";
        echo "      RewriteEngine On\n";
        echo "      RewriteCond %{REQUEST_URI} !^/public/\n";
        echo "      RewriteRule ^(.*)$ public/$1 [L]\n";
        echo "  </IfModule>\n\n";
    }
} else {
    echo "✗ Le fichier .htaccess n'existe pas à la racine du projet.\n";
    echo "  Suggestion: Créez un fichier .htaccess à la racine avec le contenu suivant:\n\n";
    echo "  <IfModule mod_rewrite.c>\n";
    echo "      RewriteEngine On\n";
    echo "      RewriteCond %{REQUEST_URI} !^/public/\n";
    echo "      RewriteRule ^(.*)$ public/$1 [L]\n";
    echo "  </IfModule>\n\n";
}

// Vérifier le fichier index.php à la racine
echo "3. Vérification du fichier index.php à la racine\n";
echo "--------------------------------------------\n";

$rootIndexPath = __DIR__ . '/../../index.php';
if (file_exists($rootIndexPath)) {
    echo "✓ Le fichier index.php existe à la racine du projet.\n\n";
} else {
    echo "✗ Le fichier index.php n'existe pas à la racine du projet.\n";
    echo "  Suggestion: Créez un fichier index.php à la racine avec le contenu suivant:\n\n";
    echo "  <?php\n\n";
    echo "  /**\n";
    echo "   * Redirect to the public directory\n";
    echo "   */\n\n";
    echo "  require_once __DIR__.'/public/index.php';\n\n";
}

// Vérifier AppServiceProvider
echo "4. Vérification du AppServiceProvider\n";
echo "-----------------------------------\n";

$appServiceProviderPath = __DIR__ . '/../../app/Providers/AppServiceProvider.php';
$appServiceProviderContent = file_get_contents($appServiceProviderPath);

if (strpos($appServiceProviderContent, 'URL::forceRootUrl') !== false) {
    echo "✓ Le AppServiceProvider contient une configuration pour forcer l'URL de base.\n\n";
} else {
    echo "✗ Le AppServiceProvider ne contient pas de configuration pour forcer l'URL de base.\n";
    echo "  Suggestion: Modifiez le fichier AppServiceProvider.php pour ajouter:\n\n";
    echo "  use Illuminate\Support\Facades\URL;\n\n";
    echo "  Dans la méthode boot():\n\n";
    echo "  // Force URLs to use the correct base path\n";
    echo "  if (env('APP_ENV') !== 'local') {\n";
    echo "      URL::forceScheme('https');\n";
    echo "  } else {\n";
    echo "      // Pour le développement local, si l'application est dans un sous-dossier\n";
    echo "      \$rootUrl = request()->getSchemeAndHttpHost();\n";
    echo "      URL::forceRootUrl(\$rootUrl);\n";
    echo "      \n";
    echo "      // Si l'application est dans un sous-dossier (par exemple /smart_school_new/public)\n";
    echo "      if (str_contains(request()->getRequestUri(), '/smart_school_new/public')) {\n";
    echo "          URL::forceRootUrl(\$rootUrl . '/smart_school_new/public');\n";
    echo "      }\n";
    echo "  }\n\n";
}

// Vérifier les liens dans les vues
echo "5. Vérification des liens dans les vues\n";
echo "------------------------------------\n";

$welcomePath = __DIR__ . '/../../resources/views/welcome.blade.php';
$welcomeContent = file_get_contents($welcomePath);

if (strpos($welcomeContent, "href=\"/login\"") !== false) {
    echo "✗ La page d'accueil contient des liens codés en dur (par exemple href=\"/login\").\n";
    echo "  Suggestion: Remplacez les liens codés en dur par des fonctions route() ou url():\n\n";
    echo "  href=\"{{ route('login') }}\" au lieu de href=\"/login\"\n\n";
} else if (strpos($welcomeContent, "href=\"{{ route('login') }}\"") !== false) {
    echo "✓ La page d'accueil utilise la fonction route() pour les liens.\n\n";
} else {
    echo "? La page d'accueil ne contient pas de lien de connexion ou utilise une autre méthode.\n\n";
}

// Résumé et recommandations
echo "=================================================================\n";
echo "                        RÉSUMÉ                                   \n";
echo "=================================================================\n\n";

echo "Pour résoudre les problèmes d'URL dans votre application Laravel, assurez-vous que:\n\n";
echo "1. Le fichier .env contient la bonne URL de base (APP_URL=" . $suggestedUrl . ")\n";
echo "2. Un fichier .htaccess existe à la racine avec des règles de redirection vers le dossier public\n";
echo "3. Un fichier index.php existe à la racine qui redirige vers public/index.php\n";
echo "4. Le AppServiceProvider est configuré pour forcer l'URL de base correcte\n";
echo "5. Tous les liens dans les vues utilisent les fonctions route() ou url() au lieu de chemins codés en dur\n\n";

echo "Ces modifications devraient résoudre les problèmes de redirection dans votre application.\n";
echo "Si vous rencontrez toujours des problèmes, vérifiez la configuration de votre serveur web.\n\n";

// Application automatique des corrections
echo "Application automatique des corrections...\n\n";

// Corriger APP_URL dans .env
$envContent = preg_replace('/APP_URL=.*/', 'APP_URL=' . $suggestedUrl, $envContent);
file_put_contents($envPath, $envContent);
echo "✓ APP_URL corrigé dans le fichier .env\n";

// Vérifier et corriger .htaccess si nécessaire
if (!file_exists($rootHtaccessPath) || strpos(file_get_contents($rootHtaccessPath), 'RewriteRule ^(.*)$ public/$1') === false) {
    $htaccessContent = "<IfModule mod_rewrite.c>\n    RewriteEngine On\n    \n    # Rediriger toutes les requêtes vers le dossier public\n    RewriteCond %{REQUEST_URI} !^/public/\n    RewriteRule ^(.*)$ public/$1 [L]\n</IfModule>";
    file_put_contents($rootHtaccessPath, $htaccessContent);
    echo "✓ Fichier .htaccess créé/corrigé à la racine\n";
}

// Vérifier et corriger index.php si nécessaire
if (!file_exists($rootIndexPath)) {
    $indexContent = "<?php\n\n/**\n * Redirect to the public directory\n */\n\nrequire_once __DIR__.'/public/index.php';";
    file_put_contents($rootIndexPath, $indexContent);
    echo "✓ Fichier index.php créé à la racine\n";
}

// Corriger AppServiceProvider si nécessaire
if (strpos($appServiceProviderContent, 'URL::forceRootUrl') === false) {
    $appServiceProviderContent = str_replace(
        'use Illuminate\Support\Facades\Schema;',
        "use Illuminate\Support\Facades\Schema;\nuse Illuminate\Support\Facades\URL;",
        $appServiceProviderContent
    );
    
    $appServiceProviderContent = str_replace(
        'Schema::defaultStringLength(191);',
        "Schema::defaultStringLength(191);\n        \n        // Force URLs to use the correct base path\n        if (env('APP_ENV') !== 'local') {\n            URL::forceScheme('https');\n        } else {\n            // Pour le développement local, si l'application est dans un sous-dossier\n            \$rootUrl = request()->getSchemeAndHttpHost();\n            URL::forceRootUrl(\$rootUrl);\n            \n            // Si l'application est dans un sous-dossier (par exemple /smart_school_new/public)\n            if (str_contains(request()->getRequestUri(), '/smart_school_new/public')) {\n                URL::forceRootUrl(\$rootUrl . '/smart_school_new/public');\n            }\n        }",
        $appServiceProviderContent
    );
    
    file_put_contents($appServiceProviderPath, $appServiceProviderContent);
    echo "✓ AppServiceProvider corrigé\n";
}

// Corriger les liens dans welcome.blade.php si nécessaire
if (strpos($welcomeContent, "href=\"/login\"") !== false) {
    $welcomeContent = str_replace('href="/login"', 'href="{{ route(\'login\') }}"', $welcomeContent);
    file_put_contents($welcomePath, $welcomeContent);
    echo "✓ Liens corrigés dans welcome.blade.php\n";
}

echo "\nToutes les corrections ont été appliquées. Veuillez redémarrer votre serveur web pour que les modifications prennent effet.\n";

echo "\n=================================================================\n";
echo "                        FIN                                      \n";
echo "=================================================================\n\n"; 