<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ESBTPRoleSeeder extends Seeder
{
    /**
     * Seeder pour créer les rôles et permissions de l'ESBTP.
     *
     * @return void
     */
    public function run()
    {
        // Création des rôles
        $roles = [
            'superadmin' => 'Super Administrateur',
            'directeur' => 'Directeur des Études',
            'enseignant' => 'Enseignant',
            'secretaire' => 'Secrétaire Académique',
            'etudiant' => 'Étudiant',
            'parent' => 'Parent d\'élève',
        ];

        foreach ($roles as $roleCode => $roleLabel) {
            Role::firstOrCreate(['name' => $roleCode, 'guard_name' => 'web']);
            $this->command->info("Rôle {$roleLabel} créé avec succès.");
        }

        // Création des permissions par module
        $permissionsParModule = [
            // Module Administration
            'administration' => [
                'view_dashboard',
                'manage_settings',
                'manage_roles',
                'manage_users',
            ],
            
            // Module Structure académique
            'structure_academique' => [
                'view_filieres',
                'create_filieres',
                'edit_filieres',
                'delete_filieres',
                
                'view_niveaux_etudes',
                'create_niveaux_etudes',
                'edit_niveaux_etudes',
                'delete_niveaux_etudes',
                
                'view_annees_universitaires',
                'create_annees_universitaires',
                'edit_annees_universitaires',
                'delete_annees_universitaires',
                
                'view_formations',
                'create_formations',
                'edit_formations',
                'delete_formations',
                
                'view_classes',
                'create_classes',
                'edit_classes',
                'delete_classes',
                
                'view_matieres',
                'create_matieres',
                'edit_matieres',
                'delete_matieres',
            ],
            
            // Module Étudiants
            'etudiants' => [
                'view_etudiants',
                'create_etudiants',
                'edit_etudiants',
                'delete_etudiants',
                
                'view_inscriptions',
                'create_inscriptions',
                'edit_inscriptions',
                'delete_inscriptions',
            ],
            
            // Module Enseignement
            'enseignement' => [
                'view_emplois_temps',
                'create_emplois_temps',
                'edit_emplois_temps',
                'delete_emplois_temps',
                
                'view_seances_cours',
                'create_seances_cours',
                'edit_seances_cours',
                'delete_seances_cours',
                
                'view_attendances',
                'create_attendances',
                'edit_attendances',
                'delete_attendances',
            ],
            
            // Module Évaluations
            'evaluations' => [
                'view_evaluations',
                'create_evaluations',
                'edit_evaluations',
                'delete_evaluations',
                
                'view_notes',
                'create_notes',
                'edit_notes',
                'delete_notes',
                
                'view_bulletins',
                'create_bulletins',
                'edit_bulletins',
                'delete_bulletins',
                'generate_bulletins',
            ],
            
            // Module Communication
            'communication' => [
                'view_annonces',
                'create_annonces',
                'edit_annonces',
                'delete_annonces',
            ],
            
            // Module Espace Étudiant
            'espace_etudiant' => [
                'view_profile',
                'view_mes_notes',
                'view_mon_emploi_temps',
            ],
        ];

        // Créer toutes les permissions
        foreach ($permissionsParModule as $module => $permissions) {
            foreach ($permissions as $permission) {
                Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
            }
        }
        
        $this->command->info("Toutes les permissions ont été créées avec succès.");

        // Assigner les permissions aux rôles
        
        // 1. Super Admin - toutes les permissions
        $superadmin = Role::findByName('superadmin', 'web');
        $allPermissions = Permission::all();
        $superadmin->syncPermissions($allPermissions);
        
        // 2. Directeur des Études
        $directeur = Role::findByName('directeur', 'web');
        $directeurPermissions = array_merge(
            $permissionsParModule['administration'],
            $permissionsParModule['structure_academique'],
            $permissionsParModule['etudiants'],
            $permissionsParModule['enseignement'],
            $permissionsParModule['evaluations'],
            $permissionsParModule['communication']
        );
        // Exclure certaines permissions réservées au superadmin
        $directeurPermissions = array_diff($directeurPermissions, ['manage_roles', 'manage_settings']);
        $directeur->syncPermissions($directeurPermissions);
        
        // 3. Enseignant
        $enseignant = Role::findByName('enseignant', 'web');
        $enseignantPermissions = array_merge(
            ['view_dashboard'],
            array_filter($permissionsParModule['structure_academique'], function($perm) {
                return strpos($perm, 'view_') === 0;
            }),
            array_filter($permissionsParModule['etudiants'], function($perm) {
                return strpos($perm, 'view_') === 0;
            }),
            $permissionsParModule['enseignement'],
            $permissionsParModule['evaluations'],
            $permissionsParModule['communication']
        );
        // Exclure certaines permissions non nécessaires pour les enseignants
        $enseignantPermissions = array_diff($enseignantPermissions, [
            'delete_seances_cours', 'delete_evaluations', 'delete_notes', 
            'edit_bulletins', 'delete_bulletins'
        ]);
        $enseignant->syncPermissions($enseignantPermissions);
        
        // 4. Secrétaire Académique
        $secretaire = Role::findByName('secretaire', 'web');
        $secretairePermissions = array_merge(
            ['view_dashboard'],
            $permissionsParModule['structure_academique'],
            $permissionsParModule['etudiants'],
            array_filter($permissionsParModule['enseignement'], function($perm) {
                return strpos($perm, 'view_') === 0 || strpos($perm, 'edit_') === 0;
            }),
            array_filter($permissionsParModule['evaluations'], function($perm) {
                return strpos($perm, 'view_') === 0 || $perm === 'generate_bulletins';
            }),
            $permissionsParModule['communication']
        );
        $secretaire->syncPermissions($secretairePermissions);
        
        // 5. Étudiant
        $etudiant = Role::findByName('etudiant', 'web');
        $etudiant->syncPermissions($permissionsParModule['espace_etudiant']);
        
        // 6. Parent
        $parent = Role::findByName('parent', 'web');
        $parent->syncPermissions(['view_profile', 'view_mes_notes']);
        
        $this->command->info("Les permissions ont été affectées aux rôles avec succès.");
    }
} 