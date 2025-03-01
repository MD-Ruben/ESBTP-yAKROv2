<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ESBTPMatiereSeeder extends Seeder
{
    /**
     * Seeder pour créer les matières des différentes filières de l'ESBTP.
     *
     * @return void
     */
    public function run()
    {
        // Récupérer l'ID de la filière MGP
        $filiereMGP = DB::table('esbtp_filieres')->where('code', 'MGP')->first();
        
        if (!$filiereMGP) {
            $this->command->error('La filière MGP n\'existe pas. Veuillez d\'abord exécuter le seeder ESBTPFiliereSeeder.');
            return;
        }
        
        // Récupérer les IDs des niveaux d'études
        $niveauBTS1 = DB::table('esbtp_niveau_etudes')->where('code', 'BTS1')->first();
        $niveauBTS2 = DB::table('esbtp_niveau_etudes')->where('code', 'BTS2')->first();
        
        if (!$niveauBTS1 || !$niveauBTS2) {
            $this->command->error('Les niveaux d\'études BTS1 ou BTS2 n\'existent pas. Veuillez d\'abord exécuter le seeder ESBTPNiveauEtudeSeeder.');
            return;
        }
        
        // Formation Générale pour MGP
        $formationGenerale = [
            [
                'code' => 'TEOC',
                'libelle' => 'Technique d\'expression écrite et orale/communication/documentation',
                'description' => 'Cours de communication et documentation technique',
                'categorie' => 'Formation Générale',
                'filiere_id' => $filiereMGP->id,
                'niveau_etude_id' => $niveauBTS1->id,
                'heures' => 40,
                'coefficient' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'TEOC2',
                'libelle' => 'Technique d\'expression écrite et orale/communication/documentation',
                'description' => 'Cours de communication et documentation technique - Niveau avancé',
                'categorie' => 'Formation Générale',
                'filiere_id' => $filiereMGP->id,
                'niveau_etude_id' => $niveauBTS2->id,
                'heures' => 20,
                'coefficient' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'ANG1',
                'libelle' => 'Anglais technique',
                'description' => 'Apprentissage de l\'anglais technique et professionnel',
                'categorie' => 'Formation Générale',
                'filiere_id' => $filiereMGP->id,
                'niveau_etude_id' => $niveauBTS1->id,
                'heures' => 40,
                'coefficient' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'ANG2',
                'libelle' => 'Anglais technique',
                'description' => 'Anglais technique avancé appliqué au domaine professionnel',
                'categorie' => 'Formation Générale',
                'filiere_id' => $filiereMGP->id,
                'niveau_etude_id' => $niveauBTS2->id,
                'heures' => 30,
                'coefficient' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'ECO1',
                'libelle' => 'Économie et Gestion',
                'description' => 'Principes d\'économie et de gestion appliqués au secteur',
                'categorie' => 'Formation Générale',
                'filiere_id' => $filiereMGP->id,
                'niveau_etude_id' => $niveauBTS1->id,
                'heures' => 30,
                'coefficient' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'DROIT1',
                'libelle' => 'Droit',
                'description' => 'Principes juridiques appliqués au secteur',
                'categorie' => 'Formation Générale',
                'filiere_id' => $filiereMGP->id,
                'niveau_etude_id' => $niveauBTS1->id,
                'heures' => 30,
                'coefficient' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'MATH1',
                'libelle' => 'Mathématiques',
                'description' => 'Mathématiques appliquées',
                'categorie' => 'Formation Générale',
                'filiere_id' => $filiereMGP->id,
                'niveau_etude_id' => $niveauBTS1->id,
                'heures' => 60,
                'coefficient' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'PHYS1',
                'libelle' => 'Physique',
                'description' => 'Statique et résistance des matériaux',
                'categorie' => 'Formation Générale',
                'filiere_id' => $filiereMGP->id,
                'niveau_etude_id' => $niveauBTS1->id,
                'heures' => 60,
                'coefficient' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'CHIM1',
                'libelle' => 'Chimie',
                'description' => 'Principes de chimie appliqués au secteur',
                'categorie' => 'Formation Générale',
                'filiere_id' => $filiereMGP->id,
                'niveau_etude_id' => $niveauBTS1->id,
                'heures' => 40,
                'coefficient' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'INFO1',
                'libelle' => 'Informatique appliquée',
                'description' => 'Initiation à l\'informatique et aux logiciels spécialisés',
                'categorie' => 'Formation Générale',
                'filiere_id' => $filiereMGP->id,
                'niveau_etude_id' => $niveauBTS1->id,
                'heures' => 40,
                'coefficient' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'CAO1',
                'libelle' => 'CAO/DAO',
                'description' => 'Conception et dessin assistés par ordinateur',
                'categorie' => 'Formation Générale',
                'filiere_id' => $filiereMGP->id,
                'niveau_etude_id' => $niveauBTS1->id,
                'heures' => 40,
                'coefficient' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'ENTR2',
                'libelle' => 'Entreprenariat',
                'description' => 'Formation à l\'entreprenariat et à la gestion de projet',
                'categorie' => 'Formation Générale',
                'filiere_id' => $filiereMGP->id,
                'niveau_etude_id' => $niveauBTS2->id,
                'heures' => 40,
                'coefficient' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        
        // Formation Technologique et Professionnelle pour MGP
        $formationPro = [
            [
                'code' => 'GEOL1',
                'libelle' => 'Géologie générale',
                'description' => 'Principes fondamentaux de géologie',
                'categorie' => 'Formation Technologique et Professionnelle',
                'filiere_id' => $filiereMGP->id,
                'niveau_etude_id' => $niveauBTS1->id,
                'heures' => 60,
                'coefficient' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'TOPO1',
                'libelle' => 'Calculs topométriques',
                'description' => 'Méthodes de calcul topométrique',
                'categorie' => 'Formation Technologique et Professionnelle',
                'filiere_id' => $filiereMGP->id,
                'niveau_etude_id' => $niveauBTS1->id,
                'heures' => 40,
                'coefficient' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'DESS1',
                'libelle' => 'Dessins plans topographiques',
                'description' => 'Techniques de dessin de plans topographiques',
                'categorie' => 'Formation Technologique et Professionnelle',
                'filiere_id' => $filiereMGP->id,
                'niveau_etude_id' => $niveauBTS1->id,
                'heures' => 40,
                'coefficient' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'TPTP1',
                'libelle' => 'Travaux pratiques de topométrie',
                'description' => 'Applications pratiques de topométrie sur le terrain',
                'categorie' => 'Formation Technologique et Professionnelle',
                'filiere_id' => $filiereMGP->id,
                'niveau_etude_id' => $niveauBTS1->id,
                'heures' => 60,
                'coefficient' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'TAPP1',
                'libelle' => 'Topométrie appliquée',
                'description' => 'Applications de la topométrie en contexte professionnel',
                'categorie' => 'Formation Technologique et Professionnelle',
                'filiere_id' => $filiereMGP->id,
                'niveau_etude_id' => $niveauBTS1->id,
                'heures' => 50,
                'coefficient' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'LOT2',
                'libelle' => 'Lotissement',
                'description' => 'Techniques et réglementation du lotissement',
                'categorie' => 'Formation Technologique et Professionnelle',
                'filiere_id' => $filiereMGP->id,
                'niveau_etude_id' => $niveauBTS2->id,
                'heures' => 50,
                'coefficient' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'PHCA2',
                'libelle' => 'Travaux de photogramétrie/cartographie',
                'description' => 'Techniques de photogrammétrie et cartographie',
                'categorie' => 'Formation Technologique et Professionnelle',
                'filiere_id' => $filiereMGP->id,
                'niveau_etude_id' => $niveauBTS2->id,
                'heures' => 60,
                'coefficient' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'GEOD2',
                'libelle' => 'Géodésie',
                'description' => 'Canevas géodésiques et référentiels spatiaux',
                'categorie' => 'Formation Technologique et Professionnelle',
                'filiere_id' => $filiereMGP->id,
                'niveau_etude_id' => $niveauBTS2->id,
                'heures' => 60,
                'coefficient' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'TAFC2',
                'libelle' => 'Travaux d\'aménagement foncier et cadastral',
                'description' => 'Droit foncier et cadastre',
                'categorie' => 'Formation Technologique et Professionnelle',
                'filiere_id' => $filiereMGP->id,
                'niveau_etude_id' => $niveauBTS2->id,
                'heures' => 50,
                'coefficient' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'OGCC2',
                'libelle' => 'Organisation et gestion des chantiers et des cabinets',
                'description' => 'Gestion de projets de terrain et de cabinets',
                'categorie' => 'Formation Technologique et Professionnelle',
                'filiere_id' => $filiereMGP->id,
                'niveau_etude_id' => $niveauBTS2->id,
                'heures' => 40,
                'coefficient' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'PHAE2',
                'libelle' => 'Photographie aérienne',
                'description' => 'Techniques de photographie aérienne (Cours, TD, TP)',
                'categorie' => 'Formation Technologique et Professionnelle',
                'filiere_id' => $filiereMGP->id,
                'niveau_etude_id' => $niveauBTS2->id,
                'heures' => 50,
                'coefficient' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'SIG2',
                'libelle' => 'Système d\'informations géographiques',
                'description' => 'Conception et utilisation des SIG',
                'categorie' => 'Formation Technologique et Professionnelle',
                'filiere_id' => $filiereMGP->id,
                'niveau_etude_id' => $niveauBTS2->id,
                'heures' => 60,
                'coefficient' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'TELE2',
                'libelle' => 'Télédétection',
                'description' => 'Principes et applications de la télédétection',
                'categorie' => 'Formation Technologique et Professionnelle',
                'filiere_id' => $filiereMGP->id,
                'niveau_etude_id' => $niveauBTS2->id,
                'heures' => 50,
                'coefficient' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'TAOG2',
                'libelle' => 'Travaux d\'auscultation d\'ouvrages et de génie civil',
                'description' => 'Techniques d\'inspection et d\'auscultation des ouvrages',
                'categorie' => 'Formation Technologique et Professionnelle',
                'filiere_id' => $filiereMGP->id,
                'niveau_etude_id' => $niveauBTS2->id,
                'heures' => 50,
                'coefficient' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'INFA2',
                'libelle' => 'Informatique appliquée',
                'description' => 'Applications informatiques spécialisées pour le secteur',
                'categorie' => 'Formation Technologique et Professionnelle',
                'filiere_id' => $filiereMGP->id,
                'niveau_etude_id' => $niveauBTS2->id,
                'heures' => 40,
                'coefficient' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'PFE2',
                'libelle' => 'Projet de fin d\'études',
                'description' => 'Réalisation d\'un projet professionnel de fin d\'études',
                'categorie' => 'Formation Technologique et Professionnelle',
                'filiere_id' => $filiereMGP->id,
                'niveau_etude_id' => $niveauBTS2->id,
                'heures' => 100,
                'coefficient' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        
        // Fusionner les matières de formation générale et professionnelle
        $matieres = array_merge($formationGenerale, $formationPro);
        
        // Utilisation de la méthode insertOrIgnore pour éviter les doublons
        DB::table('esbtp_matieres')->insertOrIgnore($matieres);
        
        $this->command->info('Les matières ESBTP ont été créées avec succès.');
    }
} 