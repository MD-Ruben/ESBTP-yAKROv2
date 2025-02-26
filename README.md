# ESBTP School Management System

Un système de gestion scolaire complet pour l'École Supérieure du Bâtiment et des Travaux Publics (ESBTP).

## Fonctionnalités

- **Gestion des utilisateurs** : Administrateurs, enseignants, étudiants et parents
- **Gestion des classes** : Création et gestion des classes et des sections
- **Gestion des emplois du temps** : Planification des cours et des horaires
- **Gestion des présences** : Suivi des présences des étudiants
- **Gestion des notes** : Enregistrement et calcul des notes des étudiants
- **Tableau de bord** : Vue d'ensemble des statistiques et des informations importantes

## Prérequis

- PHP 8.0 ou supérieur
- MySQL 5.7 ou supérieur
- Composer
- Node.js et NPM (pour la compilation des assets)
- Extensions PHP requises : PDO, mbstring, tokenizer, xml, ctype, json, bcmath

## Installation

1. Cloner le dépôt
   ```
   git clone https://github.com/votre-utilisateur/esbtp-school-management.git
   cd esbtp-school-management
   ```

2. Installer les dépendances
   ```
   composer install
   npm install
   npm run dev
   ```

3. Configurer l'environnement
   ```
   cp .env.example .env
   php artisan key:generate
   ```

4. Configurer la base de données dans le fichier `.env`
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=esbtp_school
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. Accéder à l'application
   ```
   php artisan serve
   ```
   Puis ouvrir http://localhost:8000 dans votre navigateur.

6. Suivre l'assistant d'installation qui vous guidera pour configurer la base de données et créer un compte administrateur.

## Structure du projet

- `app/` - Contient le code principal de l'application
- `config/` - Fichiers de configuration
- `database/` - Migrations et seeders pour la base de données
- `public/` - Point d'entrée et assets publics
- `resources/` - Vues, assets non compilés et fichiers de langue
- `routes/` - Définition des routes de l'application
- `setup_scripts/` - Scripts utilitaires pour l'installation et la configuration

## Développement

### Compilation des assets

```
npm run dev    # Compilation pour le développement
npm run watch  # Compilation avec hot-reload
npm run prod   # Compilation pour la production
```

### Migrations de base de données

```
php artisan migrate        # Exécuter les migrations
php artisan migrate:fresh  # Réinitialiser et exécuter toutes les migrations
php artisan db:seed        # Remplir la base de données avec des données de test
```

## Sécurité

Si vous découvrez un problème de sécurité, veuillez envoyer un e-mail à [contact@esbtp.edu](mailto:contact@esbtp.edu) au lieu d'utiliser le système d'issues.

## Licence

Ce projet est sous licence [MIT](LICENSE.md).

## Crédits

Développé pour l'École Supérieure du Bâtiment et des Travaux Publics (ESBTP).
