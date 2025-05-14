<?php
// Script pour appliquer les modifications au site KLASSCI

// Chemin vers le fichier principal
$mainFile = __DIR__ . '/../resources/views/welcome-software.blade.php';
$content = file_get_contents($mainFile);

if ($content === false) {
    die("Impossible de lire le fichier source.");
}

// 1. Modifier la taille du logo dans la navbar
$content = preg_replace(
    '/\.navbar-brand img\s*\{\s*height:\s*60px;/i',
    '.navbar-brand img { height: 75px;',
    $content
);

$content = preg_replace(
    '/\.navbar-scrolled .navbar-brand img\s*\{\s*height:\s*50px;/i',
    '.navbar-scrolled .navbar-brand img { height: 65px;',
    $content
);

// 2. Modifier la taille du logo dans le footer
$content = preg_replace(
    '/\.footer-logo img\s*\{\s*height:\s*45px;/i',
    '.footer-logo img { height: 65px; transition: transform 0.3s ease;',
    $content
);

// 3. Ajouter l'animation hover pour le logo du footer
if (strpos($content, '.footer-logo img:hover') === false) {
    $content = str_replace(
        '.footer-logo img {',
        '.footer-logo img {
        /* Styles d\'animation ajoutés */
        .footer-logo img:hover {
            transform: scale(1.05);
        }',
        $content
    );
}

// 4. Remplacer la section d'essai gratuit
$freeTrialHTML = file_get_contents(__DIR__ . '/free-trial-section.html');

// Recherche des marqueurs de début et de fin de la section d'essai gratuit
$sectionStart = '<!-- Free Trial Section -->';
$sectionEnd = '</section>';

// Trouver la position de début
$startPos = strpos($content, $sectionStart);
if ($startPos !== false) {
    // Trouver la fin de la section
    $endPos = strpos($content, $sectionEnd, $startPos);
    if ($endPos !== false) {
        $endPos += strlen($sectionEnd);
        
        // Extraire la section complète
        $oldSection = substr($content, $startPos, $endPos - $startPos);
        
        // Remplacer par la nouvelle section
        $content = str_replace($oldSection, $freeTrialHTML, $content);
    }
}

// 5. Ajouter le script JS pour les logos à la fin du fichier
$logoJS = file_get_contents(__DIR__ . '/logo-resize.js');
$jsInjectionPoint = '</body>';
$content = str_replace($jsInjectionPoint, "<script>\n" . $logoJS . "\n</script>\n" . $jsInjectionPoint, $content);

// Sauvegarder les modifications
if (file_put_contents($mainFile, $content) !== false) {
    echo "Modifications appliquées avec succès au fichier.\n";
} else {
    echo "Erreur lors de l'écriture dans le fichier.\n";
}

echo "Terminé.\n"; 