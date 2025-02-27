<?php

/**
 * Script pour nettoyer les données des structures administratives
 * 
 * Ce script permet de supprimer toutes les données créées par les seeders
 * pour la gestion des structures administratives
 */

// Chemin vers l'application Laravel
$basePath = __DIR__;

// Vérifier que nous sommes dans un projet Laravel
if (!file_exists($basePath . '/artisan')) {
    die("Ce script doit être exécuté à la racine d'un projet Laravel.\n");
}

// Afficher un message d'avertissement
echo "=================================================================\n";
echo "      NETTOYAGE DES DONNÉES DE STRUCTURES ADMINISTRATIVES         \n";
echo "=================================================================\n\n";

echo "ATTENTION : Ce script va supprimer toutes les données suivantes :\n";
echo "- Documents\n";
echo "- Évaluations\n";
echo "- Sessions de cours\n";
echo "- Salles de classe\n";
echo "- Éléments Constitutifs\n";
echo "- Unités d'Enseignement\n";
echo "- Parcours\n";
echo "- Formations\n";
echo "- UFRs (Unités de Formation et de Recherche)\n\n";

echo "Cette opération est IRRÉVERSIBLE. Assurez-vous d'avoir une sauvegarde de vos données.\n\n";

echo "Voulez-vous vraiment continuer ? (tapez 'CONFIRMER' pour continuer) : ";
$handle = fopen("php://stdin", "r");
$line = trim(fgets($handle));
if ($line !== 'CONFIRMER') {
    echo "Opération annulée.\n";
    exit;
}

// Connexion à la base de données
require $basePath . '/vendor/autoload.php';
$app = require_once $basePath . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "\nNettoyage des données...\n\n";

// Désactiver les contraintes de clés étrangères
DB::statement('SET FOREIGN_KEY_CHECKS=0;');

// Supprimer les données dans l'ordre inverse de leur création
$tables = [
    'documents',
    'evaluations',
    'course_sessions',
    'classrooms',
    'element_constitutifs',
    'unite_enseignements',
    'parcours',
    'formations',
    'ufrs'
];

foreach ($tables as $table) {
    if (Schema::hasTable($table)) {
        echo "Nettoyage de la table '$table'...\n";
        DB::table($table)->truncate();
        echo "Terminé.\n";
    } else {
        echo "La table '$table' n'existe pas, ignorée.\n";
    }
}

// Réactiver les contraintes de clés étrangères
DB::statement('SET FOREIGN_KEY_CHECKS=1;');

echo "\n=================================================================\n";
echo "      NETTOYAGE TERMINÉ AVEC SUCCÈS                               \n";
echo "=================================================================\n\n";

echo "Toutes les données des structures administratives ont été supprimées.\n";
echo "La base de données est maintenant propre.\n\n"; 