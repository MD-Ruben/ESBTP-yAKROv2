# Scripts utilitaires pour la gestion des structures administratives

Ce dossier contient des scripts utilitaires pour la gestion des structures administratives de l'application Smart School.

## Liste des scripts

### 1. run_seeders.php

Ce script permet d'exécuter tous les seeders pour initialiser la base de données avec des données de test pour les structures administratives.

**Utilisation :**
```bash
php scripts/run_seeders.php
```

**Seeders exécutés :**
- UFRsSeeder (Unités de Formation et de Recherche)
- FormationsSeeder (Formations)
- ParcoursSeeder (Parcours)
- UniteEnseignementSeeder (Unités d'Enseignement)
- ElementConstitutifSeeder (Éléments Constitutifs)
- ClassroomSeeder (Salles de classe)
- CourseSessionSeeder (Sessions de cours)
- EvaluationSeeder (Évaluations)
- DocumentSeeder (Documents)

### 2. clean_admin_structures.php

Ce script permet de nettoyer toutes les données des structures administratives de la base de données.

**Utilisation :**
```bash
php scripts/clean_admin_structures.php
```

**Tables nettoyées :**
- documents
- evaluations
- course_sessions
- classrooms
- element_constitutifs
- unite_enseignements
- parcours
- formations
- ufrs

## Précautions

- Assurez-vous d'avoir une sauvegarde de vos données avant d'exécuter le script de nettoyage.
- Ces scripts doivent être exécutés à la racine du projet Laravel.
- Le script de nettoyage demandera une confirmation avant de supprimer les données.

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

6. **Salle de Classe (Classroom)**
   - Utilisée pour les **Sessions de Cours**
   - Utilisée pour les **Évaluations**

7. **Session de Cours**
   - Liée à un **Élément Constitutif**
   - Se déroule dans une **Salle de Classe**

8. **Évaluation**
   - Liée à un **Élément Constitutif**
   - Peut se dérouler dans une **Salle de Classe**

9. **Document**
   - Lié à un **Élément Constitutif** 