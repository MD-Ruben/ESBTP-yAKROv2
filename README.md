# ESBTP-yAKRO | Système de Gestion Universitaire

Application de gestion universitaire pour l'École Supérieure de Bâtiment et Travaux Publics (ESBTP) de yAKRO.

## Fonctionnalités

-   **Gestion des classes** : Filières, formations, niveaux d'études
-   **Gestion des étudiants** : Informations personnelles, inscriptions
-   **Gestion des matières** : Organisation par niveau d'étude et filière
-   **Gestion des emplois du temps** : Planning des cours par classe
-   **Gestion des évaluations et notes** : Suivi des performances académiques
-   **Gestion des bulletins** : Génération automatique des bulletins de notes
-   **Gestion des présences** : Suivi des présences aux cours
-   **Système de rôles et permissions** : Contrôle d'accès granulaire

## Prérequis

-   PHP 8.1+
-   MySQL 5.7+ ou MariaDB 10.3+
-   Composer
-   Node.js et NPM (pour la compilation des assets)
-   Serveur web (Apache/Nginx)

## Installation

### 1. Clonage du dépôt

```bash
git clone https://github.com/votre-organisation/ESBTP-yAKRO.git
cd ESBTP-yAKRO
```

### 2. Installation des dépendances

```bash
composer install
npm install && npm run dev
```

### 3. Configuration de l'environnement

```bash
cp .env.example .env
php artisan key:generate
```

Modifiez le fichier `.env` pour configurer la connexion à la base de données et les autres paramètres.

### 4. Installation via l'interface

1. Accédez à l'URL de l'application dans votre navigateur
2. Suivez l'assistant d'installation qui vous guidera à travers les étapes suivantes :
    - Configuration de la base de données
    - Exécution des migrations
    - Création du compte administrateur
    - Finalisation de l'installation

### 5. Installation manuelle (alternative)

```bash
php artisan migrate
php artisan db:seed
```

## Structure des rôles

L'application utilise un système de rôles et permissions basé sur Spatie Laravel Permission :

1. **Super Administrateur** : Accès complet à toutes les fonctionnalités
2. **Directeur des Études** : Gestion académique
3. **Enseignant** : Gestion des notes et présences pour ses cours
4. **Secrétaire Académique** : Gestion administrative
5. **Étudiant** : Accès à son profil, ses notes et son emploi du temps
6. **Parent** : Consultation des informations de ses enfants

## Utilisation et documentation

Pour plus d'informations sur l'utilisation de l'application, consultez les fichiers de documentation :

-   [Spécifications fonctionnelles](docs/specifications_ESBTP.md)
-   [Système de rôles et permissions](docs/roles_permissions_ESBTP.md)

## Maintenance

Pour mettre à jour l'application :

```bash
git pull
composer install
php artisan migrate
php artisan optimize:clear
```

## Support

Pour toute question ou assistance, veuillez contacter l'administrateur système ou le service informatique de ESBTP-yAKRO.
