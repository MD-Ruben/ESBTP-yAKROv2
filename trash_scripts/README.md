# Scripts de Gestion des Structures Administratives

Ce dossier contient les scripts utilisés pour la gestion des structures administratives de l'application Smart School.

## Contenu du dossier

### Scripts PHP
- **run_seeders.php** - Script pour exécuter tous les seeders des structures administratives
- **clean_admin_structures.php** - Script pour nettoyer les données des structures administratives
- **update_paths.php** - Script pour mettre à jour les chemins dans les scripts

## Utilisation

### Exécution des seeders
Pour initialiser la base de données avec les données des structures administratives :
```bash
php run_seeders.php
```

### Nettoyage des données
Pour supprimer toutes les données des structures administratives :
```bash
php clean_admin_structures.php
```

### Mise à jour des chemins
Pour mettre à jour les chemins dans les scripts :
```bash
php update_paths.php
```

## Structure des données

La structure des données administratives suit le schéma suivant :

1. **UFR** (Unité de Formation et de Recherche)
   - Contient plusieurs **Formations**

2. **Formation**
   - Appartient à une **UFR**
   - Contient plusieurs **Parcours**

3. **Parcours**
   - Appartient à une **Formation**
   - Contient plusieurs **Unités d'Enseignement**

4. **Unité d'Enseignement (UE)**
   - Appartient à un **Parcours** (optionnel, peut être commun à plusieurs parcours)
   - Appartient à une **Formation**
   - Contient plusieurs **Éléments Constitutifs**

5. **Élément Constitutif (EC)**
   - Appartient à une **Unité d'Enseignement**
   - Peut avoir plusieurs **Sessions de Cours**
   - Peut avoir plusieurs **Évaluations**
   - Peut avoir plusieurs **Documents**

## Précautions
- Assurez-vous d'avoir une sauvegarde de vos données avant d'exécuter le script de nettoyage.
- Ces scripts doivent être exécutés à la racine du projet Laravel.
- Le script de nettoyage demandera une confirmation avant de supprimer les données. 