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
            'superAdmin' => 'Super Administrateur',
            'secretaire' => 'Secrétaire Académique',
            'etudiant' => 'Étudiant',
        ];

        foreach ($roles as $roleCode => $roleLabel) {
            Role::firstOrCreate(['name' => $roleCode, 'guard_name' => 'web']);
            $this->command->info("Rôle {$roleLabel} créé avec succès.");
        }

        // Liste de toutes les permissions possibles
        $allPermissions = [
            // Filières
            'create_filieres', 'view_filieres', 'edit_filieres', 'delete_filieres',
            // Niveaux d'études
            'create_niveaux_etudes', 'view_niveaux_etudes', 'edit_niveaux_etudes', 'delete_niveaux_etudes',
            // Classes
            'create_classes', 'view_classes', 'edit_classes', 'delete_classes',
            // Étudiants
            'create_students', 'view_students', 'edit_students', 'delete_students', 'view_own_profile',
            // Examens
            'create_exams', 'view_exams', 'edit_exams', 'delete_exams', 'view_own_exams',
            // Matières
            'create_matieres', 'view_matieres', 'edit_matieres', 'delete_matieres',
            // Notes
            'create_grades', 'view_grades', 'edit_grades', 'delete_grades', 'view_own_grades',
            // Bulletins
            'generate_bulletin', 'view_bulletins', 'edit_bulletins', 'delete_bulletins', 'view_own_bulletin',
            // Emplois du temps
            'create_timetable', 'view_timetables', 'edit_timetables', 'delete_timetables', 'view_own_timetable',
            // Messages
            'send_messages', 'receive_messages',
            // Présences
            'create_attendance', 'view_attendances', 'edit_attendances', 'delete_attendances', 'view_own_attendances',
            // Inscriptions
            'inscriptions.view',
            'inscriptions.create',
            'inscriptions.edit',
            'inscriptions.delete',
            'inscriptions.validate'
        ];

        // Création de toutes les permissions
        foreach ($allPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Attribution des permissions aux rôles
        $rolePermissions = [
            'superAdmin' => $allPermissions,
            'secretaire' => [
                'view_filieres', 'view_formations', 'view_niveaux_etudes', 'view_classes',
                'create_students', 'view_students', 'view_exams', 'view_matieres',
                'create_grades', 'view_grades', 'generate_bulletin', 'view_bulletins',
                'create_timetable', 'view_timetables', 'send_messages',
                'create_attendance', 'view_attendances'
            ],
            'etudiant' => [
                'view_own_profile', 'view_own_exams', 'view_own_grades', 'view_own_bulletin',
                'view_own_timetable', 'receive_messages', 'view_own_attendances'
            ]
        ];

        foreach ($rolePermissions as $roleName => $permissions) {
            $role = Role::findByName($roleName);
            $role->syncPermissions($permissions);
            $this->command->info("Permissions attribuées au rôle {$roleName}");
        }

        $this->command->info('Tous les rôles et permissions ESBTP ont été créés avec succès.');
    }
}
