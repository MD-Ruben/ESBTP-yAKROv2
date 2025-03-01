<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Réinitialiser les caches des rôles et permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Créer les permissions par catégorie
        $permissionsArray = [
            // Permissions générales
            'Général' => [
                'view-dashboard' => 'Accéder au tableau de bord',
                'manage-system' => 'Gérer les paramètres système',
            ],
            
            // Gestion des utilisateurs
            'Utilisateurs' => [
                'create-users' => 'Créer des utilisateurs',
                'view-users' => 'Voir les utilisateurs',
                'edit-users' => 'Modifier les utilisateurs',
                'delete-users' => 'Supprimer des utilisateurs',
                'view-own-profile' => 'Voir son propre profil',
                'edit-own-profile' => 'Modifier son propre profil',
            ],
            
            // Gestion académique
            'Académique' => [
                'manage-filieres' => 'Gérer les filières',
                'manage-niveaux' => 'Gérer les niveaux d\'études',
                'manage-annees' => 'Gérer les années universitaires',
                'manage-classes' => 'Gérer les classes',
                'manage-matieres' => 'Gérer les matières',
                'manage-coefficients' => 'Gérer les coefficients',
                'manage-salles' => 'Gérer les salles',
            ],
            
            // Gestion des étudiants
            'Étudiants' => [
                'create-student' => 'Créer des étudiants',
                'view-student' => 'Voir les étudiants',
                'edit-student' => 'Modifier les étudiants',
                'delete-student' => 'Supprimer des étudiants',
            ],
            
            // Notes et évaluations
            'Notes' => [
                'create-notes' => 'Créer des notes',
                'view-notes' => 'Voir les notes',
                'edit-notes' => 'Modifier les notes',
                'delete-notes' => 'Supprimer des notes',
                'view-own-notes' => 'Voir ses propres notes',
                'calculate-moyennes' => 'Calculer les moyennes',
            ],
            
            // Présences
            'Présences' => [
                'mark-presences' => 'Marquer les présences',
                'view-presences' => 'Voir les présences',
                'edit-presences' => 'Modifier les présences',
                'view-own-presences' => 'Voir ses propres présences',
            ],
            
            // Messagerie
            'Messagerie' => [
                'send-messages' => 'Envoyer des messages',
                'view-messages' => 'Voir les messages',
                'delete-messages' => 'Supprimer des messages',
                'view-own-messages' => 'Voir ses propres messages',
            ],
            
            // Emplois du temps
            'Emploi du temps' => [
                'create-emploi-temps' => 'Créer des emplois du temps',
                'view-emploi-temps' => 'Voir les emplois du temps',
                'edit-emploi-temps' => 'Modifier les emplois du temps',
                'view-own-emploi-temps' => 'Voir son propre emploi du temps',
            ],
        ];

        // Créer les permissions dans la base de données
        foreach ($permissionsArray as $category => $permissions) {
            foreach ($permissions as $name => $description) {
                Permission::create([
                    'name' => $name,
                    'category' => $category,
                    'description' => $description,
                    'guard_name' => 'web'
                ]);
            }
        }

        // Création des rôles principaux avec leurs descriptions
        $roles = [
            'superadmin' => 'Administrateur système avec accès complet à toutes les fonctionnalités',
            'data-analyst' => 'Gestionnaire de données responsable de l\'encodage et la gestion des informations',
            'etudiant' => 'Étudiant inscrit à l\'établissement',
            'parent' => 'Parent ou tuteur d\'un ou plusieurs étudiants',
            'enseignant' => 'Enseignant responsable de cours et d\'évaluations',
            'secretaire' => 'Secrétaire administratif(ve) gérant les inscriptions et les dossiers',
        ];

        foreach ($roles as $name => $description) {
            Role::create([
                'name' => $name,
                'description' => $description,
                'guard_name' => 'web'
            ]);
        }

        // Attribution des permissions aux rôles
        
        // Le superadmin a toutes les permissions
        $superadmin = Role::findByName('superadmin');
        $superadmin->givePermissionTo(Permission::all());
        
        // Data Analyst / Secrétaire académique
        $dataAnalyst = Role::findByName('data-analyst');
        $dataAnalyst->givePermissionTo([
            'view-dashboard',
            'create-users', 'view-users', 'edit-users',
            'view-own-profile', 'edit-own-profile',
            'manage-classes', 'manage-matieres',
            'create-student', 'view-student', 'edit-student',
            'create-notes', 'view-notes', 'edit-notes', 'calculate-moyennes',
            'mark-presences', 'view-presences', 'edit-presences',
            'send-messages', 'view-messages', 'delete-messages', 'view-own-messages',
            'create-emploi-temps', 'view-emploi-temps', 'edit-emploi-temps', 'view-own-emploi-temps',
        ]);
        
        // Étudiant
        $etudiant = Role::findByName('etudiant');
        $etudiant->givePermissionTo([
            'view-dashboard',
            'view-own-profile', 'edit-own-profile',
            'view-own-notes',
            'view-own-presences',
            'view-own-messages',
            'view-own-emploi-temps',
        ]);
        
        // Parent
        $parent = Role::findByName('parent');
        $parent->givePermissionTo([
            'view-dashboard',
            'view-own-profile', 'edit-own-profile',
            'view-own-messages',
        ]);
        
        // Enseignant
        $enseignant = Role::findByName('enseignant');
        $enseignant->givePermissionTo([
            'view-dashboard',
            'view-own-profile', 'edit-own-profile',
            'view-student',
            'create-notes', 'view-notes', 'edit-notes', 'calculate-moyennes',
            'mark-presences', 'view-presences',
            'send-messages', 'view-messages', 'view-own-messages',
            'view-emploi-temps', 'view-own-emploi-temps',
        ]);
        
        // Secrétaire
        $secretaire = Role::findByName('secretaire');
        $secretaire->givePermissionTo([
            'view-dashboard',
            'view-users', 'view-own-profile', 'edit-own-profile',
            'create-student', 'view-student', 'edit-student',
            'view-notes',
            'view-presences',
            'send-messages', 'view-messages', 'view-own-messages',
            'view-emploi-temps',
        ]);

        $this->command->info('Rôles et permissions créés avec succès!');
    }
} 