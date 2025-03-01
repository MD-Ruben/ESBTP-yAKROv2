<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ESBTPRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer les rôles ESBTP
        $roles = [
            'directeur' => 'Directeur de l\'école',
            'directeur_academique' => 'Directeur académique',
            'directeur_financier' => 'Directeur financier',
            'secretaire' => 'Secrétaire',
            'comptable' => 'Comptable',
            'enseignant' => 'Enseignant',
            'etudiant' => 'Étudiant',
            'parent' => 'Parent d\'étudiant',
        ];

        foreach ($roles as $name => $description) {
            Role::updateOrCreate(['name' => $name], [
                'name' => $name,
                'guard_name' => 'web',
                'description' => $description
            ]);
        }

        // Créer les permissions par module
        $permissionsByModule = [
            // Gestion des étudiants et inscriptions
            'etudiants' => [
                'etudiants.view' => 'Voir les étudiants',
                'etudiants.create' => 'Créer des étudiants',
                'etudiants.edit' => 'Modifier des étudiants',
                'etudiants.delete' => 'Supprimer des étudiants',
                'inscriptions.view' => 'Voir les inscriptions',
                'inscriptions.create' => 'Créer des inscriptions',
                'inscriptions.edit' => 'Modifier des inscriptions',
                'inscriptions.delete' => 'Supprimer des inscriptions',
                'inscriptions.validate' => 'Valider des inscriptions',
                'inscriptions.cancel' => 'Annuler des inscriptions',
            ],
            
            // Gestion académique
            'academique' => [
                'filieres.view' => 'Voir les filières',
                'filieres.manage' => 'Gérer les filières',
                'niveaux.view' => 'Voir les niveaux d\'étude',
                'niveaux.manage' => 'Gérer les niveaux d\'étude',
                'classes.view' => 'Voir les classes',
                'classes.manage' => 'Gérer les classes',
                'matieres.view' => 'Voir les matières',
                'matieres.manage' => 'Gérer les matières',
                'unites_enseignement.view' => 'Voir les unités d\'enseignement',
                'unites_enseignement.manage' => 'Gérer les unités d\'enseignement',
                'annees.view' => 'Voir les années universitaires',
                'annees.manage' => 'Gérer les années universitaires',
                'semestres.view' => 'Voir les semestres',
                'semestres.manage' => 'Gérer les semestres',
                'emplois_temps.view' => 'Voir les emplois du temps',
                'emplois_temps.manage' => 'Gérer les emplois du temps',
            ],
            
            // Gestion pédagogique
            'pedagogique' => [
                'cours.view' => 'Voir les cours',
                'cours.manage' => 'Gérer les cours',
                'examens.view' => 'Voir les examens',
                'examens.manage' => 'Gérer les examens',
                'notes.view' => 'Voir les notes',
                'notes.saisie' => 'Saisir les notes',
                'notes.validate' => 'Valider les notes',
                'notes.publish' => 'Publier les notes',
                'absences.view' => 'Voir les absences',
                'absences.manage' => 'Gérer les absences',
                'evaluations.view' => 'Voir les évaluations',
                'evaluations.manage' => 'Gérer les évaluations',
                'bulletins.view' => 'Voir les bulletins',
                'bulletins.generate' => 'Générer les bulletins',
                'bulletins.publish' => 'Publier les bulletins',
            ],
            
            // Gestion financière
            'financier' => [
                'paiements.view' => 'Voir les paiements',
                'paiements.create' => 'Créer des paiements',
                'paiements.edit' => 'Modifier des paiements',
                'paiements.delete' => 'Supprimer des paiements',
                'paiements.validate' => 'Valider des paiements',
                'paiements.cancel' => 'Annuler des paiements',
                'factures.view' => 'Voir les factures',
                'factures.manage' => 'Gérer les factures',
                'tarifs.view' => 'Voir les tarifs',
                'tarifs.manage' => 'Gérer les tarifs',
                'relances.view' => 'Voir les relances',
                'relances.manage' => 'Gérer les relances',
                'rapports_financiers.view' => 'Voir les rapports financiers',
                'rapports_financiers.generate' => 'Générer des rapports financiers',
            ],
            
            // Gestion des ressources
            'ressources' => [
                'salles.view' => 'Voir les salles',
                'salles.manage' => 'Gérer les salles',
                'equipements.view' => 'Voir les équipements',
                'equipements.manage' => 'Gérer les équipements',
                'bibliotheque.view' => 'Voir la bibliothèque',
                'bibliotheque.manage' => 'Gérer la bibliothèque',
                'documents.view' => 'Voir les documents',
                'documents.manage' => 'Gérer les documents',
            ],
            
            // Gestion des utilisateurs
            'utilisateurs' => [
                'users.view' => 'Voir les utilisateurs',
                'users.create' => 'Créer des utilisateurs',
                'users.edit' => 'Modifier des utilisateurs',
                'users.delete' => 'Supprimer des utilisateurs',
                'roles.view' => 'Voir les rôles',
                'roles.manage' => 'Gérer les rôles et permissions',
            ],
            
            // Communication
            'communication' => [
                'messages.send' => 'Envoyer des messages',
                'messages.view' => 'Voir les messages',
                'notifications.view' => 'Voir les notifications',
                'notifications.send' => 'Envoyer des notifications',
                'annonces.view' => 'Voir les annonces',
                'annonces.manage' => 'Gérer les annonces',
            ],
            
            // Système
            'systeme' => [
                'parametres.view' => 'Voir les paramètres',
                'parametres.manage' => 'Gérer les paramètres',
                'logs.view' => 'Voir les logs système',
                'backup.manage' => 'Gérer les sauvegardes',
                'statistiques.view' => 'Voir les statistiques',
            ],
        ];

        // Créer toutes les permissions
        foreach ($permissionsByModule as $module => $permissions) {
            foreach ($permissions as $name => $description) {
                Permission::updateOrCreate(['name' => $name], [
                    'name' => $name,
                    'guard_name' => 'web',
                    'description' => $description,
                    'module' => $module
                ]);
            }
        }

        // Assigner les permissions aux rôles
        
        // 1. Directeur - a toutes les permissions
        $directeur = Role::where('name', 'directeur')->first();
        $directeur->syncPermissions(Permission::all());
        
        // 2. Directeur académique - permissions académiques et pédagogiques
        $directeurAcademique = Role::where('name', 'directeur_academique')->first();
        $permissions = array_merge(
            array_keys($permissionsByModule['academique']),
            array_keys($permissionsByModule['pedagogique']),
            ['etudiants.view', 'inscriptions.view', 'statistiques.view', 'emplois_temps.view']
        );
        $directeurAcademique->syncPermissions(Permission::whereIn('name', $permissions)->get());
        
        // 3. Directeur financier - permissions financières
        $directeurFinancier = Role::where('name', 'directeur_financier')->first();
        $permissions = array_merge(
            array_keys($permissionsByModule['financier']),
            ['etudiants.view', 'inscriptions.view', 'statistiques.view']
        );
        $directeurFinancier->syncPermissions(Permission::whereIn('name', $permissions)->get());
        
        // 4. Secrétaire - inscriptions, communication, certaines vues
        $secretaire = Role::where('name', 'secretaire')->first();
        $permissions = array_merge(
            ['etudiants.view', 'etudiants.create', 'etudiants.edit',
             'inscriptions.view', 'inscriptions.create', 'inscriptions.edit',
             'paiements.view', 'factures.view', 'messages.send', 'messages.view',
             'notifications.view', 'notifications.send', 'annonces.view',
             'filieres.view', 'niveaux.view', 'classes.view', 'matieres.view',
             'emplois_temps.view', 'cours.view', 'examens.view', 'notes.view']
        );
        $secretaire->syncPermissions(Permission::whereIn('name', $permissions)->get());
        
        // 5. Comptable - paiements, factures, relances
        $comptable = Role::where('name', 'comptable')->first();
        $permissions = array_merge(
            ['etudiants.view', 'inscriptions.view',
             'paiements.view', 'paiements.create', 'paiements.edit', 'paiements.validate',
             'factures.view', 'factures.manage', 'relances.view', 'relances.manage',
             'tarifs.view', 'rapports_financiers.view']
        );
        $comptable->syncPermissions(Permission::whereIn('name', $permissions)->get());
        
        // 6. Enseignant - notes, absences, cours, etc.
        $enseignant = Role::where('name', 'enseignant')->first();
        $permissions = array_merge(
            ['etudiants.view', 'cours.view', 'examens.view',
             'notes.view', 'notes.saisie', 'absences.view', 'absences.manage',
             'evaluations.view', 'evaluations.manage', 'bulletins.view',
             'emplois_temps.view', 'messages.send', 'messages.view',
             'notifications.view', 'documents.view']
        );
        $enseignant->syncPermissions(Permission::whereIn('name', $permissions)->get());
        
        // 7. Étudiant - consultations diverses
        $etudiant = Role::where('name', 'etudiant')->first();
        $permissions = [
            'cours.view', 'examens.view', 'notes.view', 'bulletins.view',
            'emplois_temps.view', 'messages.send', 'messages.view',
            'notifications.view', 'paiements.view', 'factures.view',
            'documents.view',
        ];
        $etudiant->syncPermissions(Permission::whereIn('name', $permissions)->get());
        
        // 8. Parent - consultations diverses pour ses enfants
        $parent = Role::where('name', 'parent')->first();
        $permissions = [
            'notes.view', 'bulletins.view', 'absences.view',
            'messages.send', 'messages.view', 'notifications.view',
            'paiements.view', 'factures.view',
        ];
        $parent->syncPermissions(Permission::whereIn('name', $permissions)->get());
    }
} 