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
        // Créer les rôles de base
        $roles = [
            'super-admin',
            'admin',
            'teacher',
            'student',
            'parent'
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // Création des rôles principaux
        $roles = [
            'super-admin' => 'Accès complet à toutes les fonctionnalités',
            'admin' => 'Administrateur avec accès à la plupart des fonctionnalités',
            'directeur' => 'Directeur d\'établissement',
            'enseignant' => 'Enseignant avec accès aux cours et notes',
            'etudiant' => 'Étudiant avec accès à ses cours et notes',
            'parent' => 'Parent avec accès aux informations de ses enfants',
            'secretaire' => 'Secrétaire administrative',
            'comptable' => 'Responsable de la comptabilité',
            'bibliothecaire' => 'Responsable de la bibliothèque'
        ];

        foreach ($roles as $name => $description) {
            // Vérifier si le rôle existe déjà
            if (!Role::where('name', $name)->exists()) {
                Role::create([
                    'name' => $name,
                    'guard_name' => 'web'
                ]);
            }
        }

        // Création des permissions
        $permissions = [
            // Permissions générales
            'view dashboard' => 'Voir le tableau de bord',
            'manage users' => 'Gérer les utilisateurs',
            'view users' => 'Voir les utilisateurs',
            'create users' => 'Créer des utilisateurs',
            'edit users' => 'Modifier des utilisateurs',
            'delete users' => 'Supprimer des utilisateurs',
            
            // Permissions académiques
            'manage courses' => 'Gérer les cours',
            'view courses' => 'Voir les cours',
            'create courses' => 'Créer des cours',
            'edit courses' => 'Modifier des cours',
            'delete courses' => 'Supprimer des cours',
            
            'manage grades' => 'Gérer les notes',
            'view grades' => 'Voir les notes',
            'create grades' => 'Créer des notes',
            'edit grades' => 'Modifier des notes',
            'delete grades' => 'Supprimer des notes',
            
            // Permissions administratives
            'manage settings' => 'Gérer les paramètres',
            'view settings' => 'Voir les paramètres',
            'edit settings' => 'Modifier les paramètres',
            
            'manage finances' => 'Gérer les finances',
            'view finances' => 'Voir les finances',
            'create finances' => 'Créer des entrées financières',
            'edit finances' => 'Modifier des entrées financières',
            'delete finances' => 'Supprimer des entrées financières',
            
            // Permissions bibliothèque
            'manage library' => 'Gérer la bibliothèque',
            'view library' => 'Voir la bibliothèque',
            'create library' => 'Ajouter des livres',
            'edit library' => 'Modifier des livres',
            'delete library' => 'Supprimer des livres',
            'borrow library' => 'Emprunter des livres',
            'return library' => 'Retourner des livres'
        ];

        foreach ($permissions as $name => $description) {
            // Vérifier si la permission existe déjà
            if (!Permission::where('name', $name)->exists()) {
                Permission::create([
                    'name' => $name,
                    'guard_name' => 'web'
                ]);
            }
        }

        // Attribution des permissions aux rôles
        $superAdmin = Role::findByName('super-admin');
        $superAdmin->syncPermissions(Permission::all());

        $admin = Role::findByName('admin');
        $admin->syncPermissions(Permission::all()->except(['manage settings', 'edit settings']));

        $directeur = Role::findByName('directeur');
        $directeur->syncPermissions([
            'view dashboard', 'view users', 
            'manage courses', 'view courses', 'create courses', 'edit courses', 
            'manage grades', 'view grades', 
            'view finances', 
            'view library'
        ]);

        $enseignant = Role::findByName('enseignant');
        $enseignant->syncPermissions([
            'view dashboard', 
            'view courses', 'edit courses', 
            'manage grades', 'view grades', 'create grades', 'edit grades', 
            'view library', 'borrow library', 'return library'
        ]);

        $etudiant = Role::findByName('etudiant');
        $etudiant->syncPermissions([
            'view dashboard', 
            'view courses', 
            'view grades', 
            'view library', 'borrow library', 'return library'
        ]);

        $parent = Role::findByName('parent');
        $parent->syncPermissions([
            'view dashboard', 
            'view courses', 
            'view grades', 
            'view finances'
        ]);

        $secretaire = Role::findByName('secretaire');
        $secretaire->syncPermissions([
            'view dashboard', 
            'view users', 'create users', 'edit users', 
            'view courses', 
            'view grades', 
            'view finances'
        ]);

        $comptable = Role::findByName('comptable');
        $comptable->syncPermissions([
            'view dashboard', 
            'manage finances', 'view finances', 'create finances', 'edit finances', 'delete finances'
        ]);

        $bibliothecaire = Role::findByName('bibliothecaire');
        $bibliothecaire->syncPermissions([
            'view dashboard', 
            'manage library', 'view library', 'create library', 'edit library', 'delete library', 'borrow library', 'return library'
        ]);
    }
} 