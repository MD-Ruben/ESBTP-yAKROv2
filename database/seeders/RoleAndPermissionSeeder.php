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

        // Vérifier si les tables sont vides ou si nous devons réinitialiser
        if ($this->shouldReset()) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('model_has_permissions')->truncate();
            DB::table('model_has_roles')->truncate();
            DB::table('role_has_permissions')->truncate();
            DB::table('permissions')->truncate();
            DB::table('roles')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            
            $this->command->info('Tables des permissions et rôles réinitialisées');
        } else {
            // Sinon, nous allons mettre à jour les permissions existantes
            $this->command->info('Mise à jour des permissions existantes');
        }

        // Création des permissions
        $permissions = [
            // Filières
            'create filieres', 'view filieres', 'edit filieres', 'delete filieres',
            
            // Formations
            'create formations', 'view formations', 'edit formations', 'delete formations',
            
            // Niveaux d'études
            'create niveaux etudes', 'view niveaux etudes', 'edit niveaux etudes', 'delete niveaux etudes',
            
            // Classes
            'create classes', 'view classes', 'edit classes', 'delete classes',
            
            // Étudiants
            'create students', 'view students', 'edit students', 'delete students', 'view own profile',
            
            // Examens
            'create exams', 'view exams', 'edit exams', 'delete exams', 'view own exams',
            
            // Matières
            'create matieres', 'view matieres', 'edit matieres', 'delete matieres',
            
            // Notes
            'create grades', 'view grades', 'edit grades', 'delete grades', 'view own grades',
            
            // Bulletins
            'generate bulletin', 'view bulletins', 'edit bulletins', 'delete bulletins', 'view own bulletin',
            
            // Emplois du temps
            'create timetable', 'view timetables', 'edit timetables', 'delete timetables', 'view own timetable',
            
            // Messages
            'send messages', 'receive messages',
            
            // Présences
            'create attendance', 'view attendances', 'edit attendances', 'delete attendances', 'view own attendances',
        ];

        // Créer les permissions si elles n'existent pas déjà
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
        
        $this->command->info(count($permissions) . ' permissions créées ou mises à jour');

        // Définition des rôles et leurs permissions associées
        $roles = [
            'superAdmin' => [
                'create filieres', 'view filieres', 'edit filieres', 'delete filieres',
                'create formations', 'view formations', 'edit formations', 'delete formations',
                'create niveaux etudes', 'view niveaux etudes', 'edit niveaux etudes', 'delete niveaux etudes',
                'create classes', 'view classes', 'edit classes', 'delete classes',
                'create students', 'view students', 'edit students', 'delete students',
                'create exams', 'view exams', 'edit exams', 'delete exams',
                'create matieres', 'view matieres', 'edit matieres', 'delete matieres',
                'create grades', 'view grades', 'edit grades', 'delete grades',
                'generate bulletin', 'view bulletins', 'edit bulletins', 'delete bulletins',
                'create timetable', 'view timetables', 'edit timetables', 'delete timetables',
                'send messages', 'receive messages',
                'create attendance', 'view attendances', 'edit attendances', 'delete attendances'
            ],
            'secretaire' => [
                'view filieres',
                'view formations',
                'view niveaux etudes',
                'view classes',
                'create students', 'view students',
                'view exams',
                'view matieres',
                'create grades', 'view grades',
                'generate bulletin', 'view bulletins',
                'create timetable', 'view timetables',
                'send messages', 'receive messages',
                'create attendance', 'view attendances'
            ],
            'etudiant' => [
                'view own profile',
                'view own exams',
                'view own grades',
                'view own bulletin',
                'view own timetable',
                'receive messages',
                'view own attendances'
            ],
            'parent' => [
                'view students', // Pour voir les profils de ses enfants
                'view own profile',
                'view exams', // Pour voir les examens de ses enfants
                'view grades', // Pour voir les notes de ses enfants
                'view bulletins', // Pour voir les bulletins de ses enfants
                'view timetables', // Pour voir les emplois du temps de ses enfants
                'send messages', 'receive messages',
                'view attendances' // Pour voir les présences de ses enfants
            ]
        ];

        // Créer les rôles et assigner les permissions
        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->syncPermissions($rolePermissions);
            $this->command->info("Rôle '{$roleName}' créé ou mis à jour avec " . count($rolePermissions) . " permissions");
        }
    }

    /**
     * Détermine si les tables doivent être réinitialisées.
     * 
     * @return bool
     */
    private function shouldReset()
    {
        // Vérifier si nous sommes dans un environnement de développement
        if (app()->environment('local')) {
            // Dans l'environnement local, demander à l'utilisateur s'il souhaite réinitialiser
            if ($this->command->confirm('Voulez-vous réinitialiser les tables de rôles et permissions?', false)) {
                return true;
            }
        }
        
        // Par défaut, ne pas réinitialiser
        return false;
    }
} 