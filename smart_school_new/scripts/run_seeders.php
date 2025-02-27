<?php

/**
 * Script pour exécuter tous les seeders
 * 
 * Ce script permet d'exécuter tous les seeders créés pour la gestion des structures administratives
 * Il est utile pour initialiser rapidement la base de données avec des données de test
 */

// Chemin vers l'application Laravel
$basePath = __DIR__;

// Vérifier que nous sommes dans un projet Laravel
if (!file_exists($basePath . '/artisan')) {
    die("Ce script doit être exécuté à la racine d'un projet Laravel.\n");
}

// Afficher un message de bienvenue
echo "=================================================================\n";
echo "      INITIALISATION DES DONNÉES DE STRUCTURES ADMINISTRATIVES    \n";
echo "=================================================================\n\n";

echo "Ce script va exécuter les seeders suivants :\n";
echo "- UFRsSeeder (Unités de Formation et de Recherche)\n";
echo "- FormationsSeeder (Formations)\n";
echo "- ParcoursSeeder (Parcours)\n";
echo "- UniteEnseignementSeeder (Unités d'Enseignement)\n";
echo "- ElementConstitutifSeeder (Éléments Constitutifs)\n";
echo "- ClassroomSeeder (Salles de classe)\n";
echo "- CourseSessionSeeder (Sessions de cours)\n";
echo "- EvaluationSeeder (Évaluations)\n";
echo "- DocumentSeeder (Documents)\n\n";

echo "Voulez-vous continuer ? (o/n) : ";
$handle = fopen("php://stdin", "r");
$line = trim(fgets($handle));
if (strtolower($line) !== 'o') {
    echo "Opération annulée.\n";
    exit;
}

// Exécuter les seeders
echo "\nExécution des seeders...\n\n";

// Exécuter chaque seeder individuellement pour un meilleur contrôle
$seeders = [
    'UFRsSeeder',
    'FormationsSeeder',
    'ParcoursSeeder',
    'UniteEnseignementSeeder',
    'ElementConstitutifSeeder',
    'ClassroomSeeder',
    'CourseSessionSeeder',
    'EvaluationSeeder',
    'DocumentSeeder'
];

foreach ($seeders as $seeder) {
    echo "Exécution de $seeder...\n";
    system("php artisan db:seed --class=$seeder");
    echo "Terminé.\n\n";
}

echo "=================================================================\n";
echo "      INITIALISATION TERMINÉE AVEC SUCCÈS                         \n";
echo "=================================================================\n\n";

echo "Les données de structures administratives ont été initialisées avec succès.\n";
echo "Vous pouvez maintenant accéder à l'application et explorer les données.\n\n"; 