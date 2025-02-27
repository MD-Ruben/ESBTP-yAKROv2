<?php

/**
 * Script pour vérifier les routes de l'application
 * 
 * Ce script affiche toutes les routes définies dans l'application
 * et vérifie si les routes d'authentification sont correctement configurées.
 */

// Charger l'application Laravel
require __DIR__ . '/../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Afficher l'en-tête
echo "\n";
echo "=================================================================\n";
echo "      VÉRIFICATION DES ROUTES DE L'APPLICATION                   \n";
echo "=================================================================\n\n";

// Récupérer toutes les routes
$routes = app('router')->getRoutes();
$authRoutes = [];
$webRoutes = [];
$apiRoutes = [];
$otherRoutes = [];

// Trier les routes par type
foreach ($routes as $route) {
    $name = $route->getName();
    $uri = $route->uri();
    $methods = implode('|', $route->methods());
    $action = $route->getActionName();
    
    $routeInfo = [
        'name' => $name,
        'uri' => $uri,
        'methods' => $methods,
        'action' => $action,
    ];
    
    if (strpos($name, 'login') !== false || strpos($name, 'logout') !== false || 
        strpos($name, 'register') !== false || strpos($name, 'password') !== false || 
        strpos($name, 'verification') !== false || strpos($name, 'confirm') !== false) {
        $authRoutes[$name] = $routeInfo;
    } else if (strpos($uri, 'api/') === 0) {
        $apiRoutes[$name] = $routeInfo;
    } else if (in_array('web', $route->middleware())) {
        $webRoutes[$name] = $routeInfo;
    } else {
        $otherRoutes[$name] = $routeInfo;
    }
}

// Afficher les routes d'authentification
echo "Routes d'authentification:\n";
echo "-------------------------\n\n";

if (count($authRoutes) > 0) {
    echo "| " . str_pad("Nom", 30) . " | " . str_pad("URI", 30) . " | " . str_pad("Méthodes", 15) . " | " . str_pad("Action", 50) . " |\n";
    echo "|" . str_repeat("-", 32) . "|" . str_repeat("-", 32) . "|" . str_repeat("-", 17) . "|" . str_repeat("-", 52) . "|\n";
    
    foreach ($authRoutes as $name => $route) {
        echo "| " . str_pad($name ?? 'N/A', 30) . " | " . str_pad($route['uri'], 30) . " | " . str_pad($route['methods'], 15) . " | " . str_pad($route['action'], 50) . " |\n";
    }
} else {
    echo "Aucune route d'authentification trouvée.\n";
}

echo "\n";

// Vérifier les routes d'authentification essentielles
$requiredAuthRoutes = [
    'login' => false,
    'logout' => false,
    'register' => false,
    'password.request' => false,
    'password.email' => false,
    'password.reset' => false,
    'password.update' => false,
    'verification.notice' => false,
    'verification.verify' => false,
    'verification.resend' => false,
];

foreach ($authRoutes as $name => $route) {
    foreach ($requiredAuthRoutes as $requiredRoute => $found) {
        if ($name === $requiredRoute) {
            $requiredAuthRoutes[$requiredRoute] = true;
        }
    }
}

// Afficher les routes manquantes
$missingRoutes = [];
foreach ($requiredAuthRoutes as $route => $found) {
    if (!$found) {
        $missingRoutes[] = $route;
    }
}

if (count($missingRoutes) > 0) {
    echo "Routes d'authentification manquantes:\n";
    echo "-----------------------------------\n\n";
    
    foreach ($missingRoutes as $route) {
        echo "- " . $route . "\n";
    }
    
    echo "\nSuggestion: Assurez-vous que les routes d'authentification sont correctement configurées.\n";
    echo "Vous pouvez ajouter les routes manquantes en utilisant Auth::routes() dans routes/web.php.\n\n";
} else {
    echo "✓ Toutes les routes d'authentification essentielles sont présentes.\n\n";
}

// Vérifier la route de redirection après connexion
$homeRoute = app('router')->getRoutes()->getByName('home');
$dashboardRoute = app('router')->getRoutes()->getByName('dashboard');

echo "Route de redirection après connexion:\n";
echo "-----------------------------------\n\n";

$redirectTo = config('auth.redirectTo', '/home');
echo "Redirection configurée vers: " . $redirectTo . "\n";

if ($redirectTo === '/home' && $homeRoute) {
    echo "✓ La route 'home' existe et correspond à la redirection configurée.\n";
} else if ($redirectTo === '/dashboard' && $dashboardRoute) {
    echo "✓ La route 'dashboard' existe et correspond à la redirection configurée.\n";
} else if ($homeRoute) {
    echo "! La redirection est configurée vers '" . $redirectTo . "', mais la route 'home' existe.\n";
    echo "  Suggestion: Modifiez la redirection pour utiliser la route 'home' ou créez une route pour '" . $redirectTo . "'.\n";
} else if ($dashboardRoute) {
    echo "! La redirection est configurée vers '" . $redirectTo . "', mais la route 'dashboard' existe.\n";
    echo "  Suggestion: Modifiez la redirection pour utiliser la route 'dashboard' ou créez une route pour '" . $redirectTo . "'.\n";
} else {
    echo "✗ Aucune route 'home' ou 'dashboard' n'a été trouvée.\n";
    echo "  Suggestion: Créez une route pour '" . $redirectTo . "' ou modifiez la redirection dans config/auth.php.\n";
}

echo "\n";

// Vérifier les middlewares d'authentification
echo "Middlewares d'authentification:\n";
echo "-----------------------------\n\n";

$authMiddleware = app('router')->getMiddleware()['auth'] ?? null;
$guestMiddleware = app('router')->getMiddleware()['guest'] ?? null;

if ($authMiddleware) {
    echo "✓ Middleware 'auth' configuré: " . $authMiddleware . "\n";
} else {
    echo "✗ Middleware 'auth' non configuré.\n";
}

if ($guestMiddleware) {
    echo "✓ Middleware 'guest' configuré: " . $guestMiddleware . "\n";
} else {
    echo "✗ Middleware 'guest' non configuré.\n";
}

echo "\n";

// Résumé et recommandations
echo "=================================================================\n";
echo "                        RÉSUMÉ                                   \n";
echo "=================================================================\n\n";

echo "Nombre total de routes: " . count($routes) . "\n";
echo "- Routes d'authentification: " . count($authRoutes) . "\n";
echo "- Routes web: " . count($webRoutes) . "\n";
echo "- Routes API: " . count($apiRoutes) . "\n";
echo "- Autres routes: " . count($otherRoutes) . "\n\n";

if (count($missingRoutes) > 0) {
    echo "✗ " . count($missingRoutes) . " routes d'authentification manquantes.\n";
} else {
    echo "✓ Toutes les routes d'authentification essentielles sont présentes.\n";
}

echo "\n=================================================================\n";
echo "                        FIN                                      \n";
echo "=================================================================\n\n"; 