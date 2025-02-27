# ESBTP School Management System

## À propos

ESBTP School Management System est une application web complète pour la gestion des écoles, spécialement conçue pour l'École Supérieure du Bâtiment et des Travaux Publics (ESBTP). Cette application permet de gérer les départements, les cycles de formation, les spécialités, les années d'études, les semestres, les partenariats et la formation continue.

## Prérequis

- PHP 7.4 ou supérieur
- Composer
- MySQL 5.7 ou supérieur
- Node.js et NPM (pour la compilation des assets)
- Serveur web (Apache, Nginx, etc.)

## Installation

1. Clonez le dépôt :
   ```
   git clone https://github.com/votre-utilisateur/esbtp-school-management.git
   cd esbtp-school-management
   ```

2. Installez les dépendances PHP :
   ```
   composer install
   ```

3. Installez les dépendances JavaScript :
   ```
   npm install && npm run dev
   ```

4. Copiez le fichier d'environnement :
   ```
   cp .env.example .env
   ```

5. Configurez votre base de données dans le fichier `.env` :
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=esbtp_school
   DB_USERNAME=root
   DB_PASSWORD=
   ```

6. Générez une clé d'application :
   ```
   php artisan key:generate
   ```

7. Exécutez les migrations et les seeders :
   ```
   php artisan migrate --seed
   ```

8. Créez un lien symbolique pour le stockage :
   ```
   php artisan storage:link
   ```

## Scripts utilitaires

Le dossier `scripts_esbtp` contient plusieurs scripts utilitaires pour faciliter la gestion de l'application :

### Réinitialisation de l'application

Pour réinitialiser l'application à son état initial :

- Sur Windows : exécutez `scripts_esbtp/reset_app.bat`
- Sur Linux/Mac : exécutez `scripts_esbtp/reset_app.sh` (assurez-vous qu'il est exécutable avec `chmod +x scripts_esbtp/reset_app.sh`)

### Démarrage de l'application

Pour démarrer le serveur de développement :

- Sur Windows : exécutez `scripts_esbtp/start_app.bat`
- Sur Linux/Mac : exécutez `scripts_esbtp/start_app.sh` (assurez-vous qu'il est exécutable avec `chmod +x scripts_esbtp/start_app.sh`)

### Mise à jour de l'application

Pour mettre à jour l'application avec les dernières modifications :

- Sur Windows : exécutez `scripts_esbtp/update_app.bat`
- Sur Linux/Mac : exécutez `scripts_esbtp/update_app.sh` (assurez-vous qu'il est exécutable avec `chmod +x scripts_esbtp/update_app.sh`)

### Sauvegarde de la base de données

Pour créer une sauvegarde de la base de données :

- Sur Windows : exécutez `scripts_esbtp/backup_db.bat`
- Sur Linux/Mac : exécutez `scripts_esbtp/backup_db.sh` (assurez-vous qu'il est exécutable avec `chmod +x scripts_esbtp/backup_db.sh`)

Les sauvegardes sont stockées dans le dossier `storage/app/backups` avec un horodatage dans le nom du fichier.

### Création d'un package d'installation

Pour créer un package d'installation léger et épuré de l'application :

- Sur Windows : exécutez `scripts_esbtp/create_package.bat`
- Sur Linux/Mac : exécutez `scripts_esbtp/create_package.sh` (assurez-vous qu'il est exécutable avec `chmod +x scripts_esbtp/create_package.sh`)

Le package créé contiendra :
- Le code source de l'application (sans les dossiers vendor et node_modules)
- Des scripts d'installation pour Windows et Linux/Mac
- Un guide d'installation rapide
- Les identifiants par défaut

Ce package peut être facilement distribué aux clients ou stocké sur une clé USB ou en ligne.

Pour plus d'informations sur ces scripts, consultez le fichier `scripts_esbtp/README.md`.

## Identifiants par défaut

### Superadmin
- Email : admin@esbtp.ci
- Mot de passe : admin123

## Fonctionnalités

- Gestion des départements
- Gestion des cycles de formation
- Gestion des spécialités
- Gestion des années d'études
- Gestion des semestres
- Gestion des partenariats
- Gestion de la formation continue
- Gestion des utilisateurs et des rôles
- Et bien plus encore...

## Structure du projet

- `app/` - Contient les modèles, contrôleurs et autres classes PHP
- `config/` - Contient les fichiers de configuration
- `database/` - Contient les migrations et les seeders
- `public/` - Contient les fichiers accessibles publiquement
- `resources/` - Contient les vues, les assets et les fichiers de traduction
- `routes/` - Contient les définitions de routes
- `storage/` - Contient les fichiers téléchargés, les logs, etc.
- `tests/` - Contient les tests automatisés
- `scripts_esbtp/` - Contient les scripts utilitaires pour l'application

## Contribution

Les contributions sont les bienvenues ! N'hésitez pas à ouvrir une issue ou à soumettre une pull request.

## Licence

Ce projet est sous licence [MIT](LICENSE).
