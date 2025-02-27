# ESBTP - École Supérieure du Bâtiment et des Travaux Publics

![Logo ESBTP](public/img/esbtp_logo.png)

## À propos du projet

Ce projet est une application web pour l'École Supérieure du Bâtiment et des Travaux Publics (ESBTP) de Yamoussoukro, Côte d'Ivoire. L'application permet de gérer les étudiants, les enseignants, les cours, les emplois du temps et d'autres aspects administratifs de l'école.

## Améliorations récentes

### Refonte de la page d'accueil
- Design moderne avec Bootstrap 5
- Animations au défilement avec AOS
- Sections structurées (À propos, Formations, Contact)
- Navigation améliorée et responsive

### Mise à jour des informations de contact
- Coordonnées réelles de l'ESBTP Yamoussoukro
- Intégration d'une carte Google Maps interactive
- Formulaire de contact fonctionnel

### Optimisation multi-plateforme
- Compatibilité Windows et Linux
- Scripts de maintenance et de sauvegarde
- Documentation complète du projet

## Structure du projet

```
smart_school_new/
├── app/                  # Code source Laravel
├── bootstrap/            # Fichiers d'initialisation Laravel
├── config/               # Configuration de l'application
├── database/             # Migrations et seeders
├── public/               # Fichiers accessibles publiquement
│   ├── css/              # Feuilles de style CSS
│   ├── img/              # Images et logos
│   └── js/               # Scripts JavaScript
├── resources/            # Ressources non compilées
│   ├── views/            # Templates Blade
│   │   └── welcome.blade.php  # Page d'accueil
│   ├── js/               # JavaScript source
│   └── css/              # CSS source
├── routes/               # Définition des routes
├── scripts_projet_esbtp/ # Scripts de maintenance
│   ├── backup_projet.ps1     # Sauvegarde des fichiers
│   ├── check_integrity.ps1   # Vérification d'intégrité
│   ├── create_logos.ps1      # Création des logos
│   ├── README.md             # Documentation des scripts
│   └── documentation.md      # Documentation complète
└── storage/              # Fichiers générés par l'application
```

## Technologies utilisées

- **Backend**: Laravel (PHP)
- **Frontend**: HTML5, CSS3, JavaScript, Bootstrap 5
- **Base de données**: MySQL
- **Outils**: PowerShell (scripts de maintenance)

## Installation

1. Cloner le dépôt
2. Installer les dépendances avec Composer: `composer install`
3. Configurer le fichier `.env` avec les informations de la base de données
4. Exécuter les migrations: `php artisan migrate`
5. Lancer les seeders: `php artisan db:seed`
6. Démarrer le serveur: `php artisan serve`

## Maintenance

Pour maintenir l'application:

1. Exécuter régulièrement le script de sauvegarde: `.\scripts_projet_esbtp\backup_projet.ps1`
2. Vérifier l'intégrité des fichiers: `.\scripts_projet_esbtp\check_integrity.ps1`
3. Consulter la documentation complète dans `scripts_projet_esbtp/documentation.md`

## Licence

Ce projet est la propriété de l'École Supérieure du Bâtiment et des Travaux Publics (ESBTP) de Yamoussoukro.

## Contact

Pour toute question concernant ce projet, veuillez contacter:

- **ESBTP Yamoussoukro**
- Quartier Millionnaire, Yamoussoukro, Côte d'Ivoire
- Téléphone: +225 27 30 64 66 75 / +225 07 07 43 43 75
- Email: info@esbtp-ci.net
- Site web: www.esbtp-ci.net
