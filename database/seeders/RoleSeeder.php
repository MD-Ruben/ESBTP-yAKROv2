<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Création des rôles principaux pour ESBTP
        $roles = [
            'superadmin',
            'secretaire',
            'enseignant',
            'etudiant',
            'parent'
        ];

        foreach ($roles as $roleName) {
            Role::create(['name' => $roleName]);
        }

        // Création des permissions de base
        // Pour les filières
        Permission::create(['name' => 'filieres.view']);
        Permission::create(['name' => 'filieres.create']);
        Permission::create(['name' => 'filieres.edit']);
        Permission::create(['name' => 'filieres.delete']);

        // Pour les niveaux d'études
        Permission::create(['name' => 'niveaux-etudes.view']);
        Permission::create(['name' => 'niveaux-etudes.create']);
        Permission::create(['name' => 'niveaux-etudes.edit']);
        Permission::create(['name' => 'niveaux-etudes.delete']);

        // Pour les formations
        Permission::create(['name' => 'formations.view']);
        Permission::create(['name' => 'formations.create']);
        Permission::create(['name' => 'formations.edit']);
        Permission::create(['name' => 'formations.delete']);

        // Pour les matières
        Permission::create(['name' => 'matieres.view']);
        Permission::create(['name' => 'matieres.create']);
        Permission::create(['name' => 'matieres.edit']);
        Permission::create(['name' => 'matieres.delete']);

        // Pour les classes
        Permission::create(['name' => 'classes.view']);
        Permission::create(['name' => 'classes.create']);
        Permission::create(['name' => 'classes.edit']);
        Permission::create(['name' => 'classes.delete']);

        // Pour les étudiants
        Permission::create(['name' => 'etudiants.view']);
        Permission::create(['name' => 'etudiants.create']);
        Permission::create(['name' => 'etudiants.edit']);
        Permission::create(['name' => 'etudiants.delete']);

        // Pour les évaluations
        Permission::create(['name' => 'evaluations.view']);
        Permission::create(['name' => 'evaluations.create']);
        Permission::create(['name' => 'evaluations.edit']);
        Permission::create(['name' => 'evaluations.delete']);

        // Pour les notes
        Permission::create(['name' => 'notes.view']);
        Permission::create(['name' => 'notes.create']);
        Permission::create(['name' => 'notes.edit']);
        Permission::create(['name' => 'notes.delete']);

        // Pour les bulletins
        Permission::create(['name' => 'bulletins.view']);
        Permission::create(['name' => 'bulletins.create']);
        Permission::create(['name' => 'bulletins.edit']);
        Permission::create(['name' => 'bulletins.delete']);

        // Pour les emplois du temps
        Permission::create(['name' => 'emplois-temps.view']);
        Permission::create(['name' => 'emplois-temps.create']);
        Permission::create(['name' => 'emplois-temps.edit']);
        Permission::create(['name' => 'emplois-temps.delete']);

        // Pour les annonces
        Permission::create(['name' => 'annonces.view']);
        Permission::create(['name' => 'annonces.create']);
        Permission::create(['name' => 'annonces.edit']);
        Permission::create(['name' => 'annonces.delete']);

        // Attribution des permissions aux rôles
        // Le superadmin a toutes les permissions
        $superadminRole = Role::findByName('superadmin');
        $superadminRole->givePermissionTo(Permission::all());

        // La secrétaire a des permissions administratives
        $secretaireRole = Role::findByName('secretaire');
        $secretaireRole->givePermissionTo([
            'filieres.view', 'filieres.create', 'filieres.edit',
            'niveaux-etudes.view', 'niveaux-etudes.create', 'niveaux-etudes.edit',
            'formations.view', 'formations.create', 'formations.edit',
            'matieres.view', 'matieres.create', 'matieres.edit',
            'classes.view', 'classes.create', 'classes.edit',
            'etudiants.view', 'etudiants.create', 'etudiants.edit',
            'evaluations.view',
            'notes.view',
            'bulletins.view', 'bulletins.create',
            'emplois-temps.view', 'emplois-temps.create', 'emplois-temps.edit',
            'annonces.view', 'annonces.create', 'annonces.edit'
        ]);

        // L'enseignant a des permissions liées à l'enseignement
        $enseignantRole = Role::findByName('enseignant');
        $enseignantRole->givePermissionTo([
            'matieres.view',
            'classes.view',
            'etudiants.view',
            'evaluations.view', 'evaluations.create', 'evaluations.edit',
            'notes.view', 'notes.create', 'notes.edit',
            'bulletins.view',
            'emplois-temps.view'
        ]);

        // L'étudiant a des permissions limitées
        $etudiantRole = Role::findByName('etudiant');
        $etudiantRole->givePermissionTo([
            'bulletins.view',
            'emplois-temps.view',
            'annonces.view'
        ]);

        // Le parent a des permissions similaires à l'étudiant
        $parentRole = Role::findByName('parent');
        $parentRole->givePermissionTo([
            'bulletins.view',
            'emplois-temps.view',
            'annonces.view'
        ]);
    }
} 