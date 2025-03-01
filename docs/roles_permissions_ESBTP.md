# Système de rôles et permissions - Application ESBTP

Ce document définit le système de rôles et permissions pour l'application de gestion ESBTP.

## Rôles principaux

1. **Super Administrateur**

    - Accès complet à toutes les fonctionnalités du système
    - Gestion des autres utilisateurs et leurs rôles
    - Configuration générale du système

2. **Administrateur**

    - Accès à la majorité des fonctionnalités administratives
    - Gestion des données principales (classes, matières, emplois du temps)
    - Gestion des inscriptions

3. **Directeur des Études**

    - Supervision des aspects académiques
    - Gestion des évaluations et des bulletins
    - Consultation des statistiques

4. **Enseignant**

    - Gestion des notes pour ses propres cours
    - Gestion des présences pour ses propres cours
    - Envoi d'annonces à ses classes

5. **Secrétaire Académique**

    - Gestion des dossiers étudiants
    - Suivi des présences
    - Gestion des annonces générales

6. **Étudiant**

    - Consultation de son profil
    - Consultation de ses notes et bulletins
    - Consultation de son emploi du temps
    - Réception des annonces

7. **Parent**
    - Consultation des informations de ses enfants
    - Consultation des notes et bulletins
    - Consultation des présences
    - Réception des annonces

## Matrice des permissions

| Fonctionnalité                   | Super Admin | Admin | Directeur Études | Enseignant | Secrétaire | Étudiant | Parent |
| -------------------------------- | ----------- | ----- | ---------------- | ---------- | ---------- | -------- | ------ |
| **Gestion des utilisateurs**     | CRUD        | CR    | R                | -          | -          | -        | -      |
| **Gestion des rôles**            | CRUD        | R     | -                | -          | -          | -        | -      |
| **Configuration système**        | CRUD        | R     | -                | -          | -          | -        | -      |
| **Gestion des classes**          | CRUD        | CRUD  | R                | R          | R          | R        | R      |
| **Gestion des filières**         | CRUD        | CRUD  | R                | R          | R          | R        | R      |
| **Gestion des formations**       | CRUD        | CRUD  | R                | R          | R          | R        | R      |
| **Gestion des niveaux d'étude**  | CRUD        | CRUD  | R                | R          | R          | R        | R      |
| **Gestion des matières**         | CRUD        | CRUD  | CRUD             | R          | R          | R        | R      |
| **Gestion des emplois du temps** | CRUD        | CRUD  | CRUD             | R          | R          | R        | R      |
| **Gestion des étudiants**        | CRUD        | CRUD  | CR               | R          | CRUD       | R\*      | R\*    |
| **Gestion des parents**          | CRUD        | CRUD  | R                | R          | CRUD       | -        | R\*    |
| **Gestion des inscriptions**     | CRUD        | CRUD  | CR               | -          | CR         | -        | -      |
| **Gestion des évaluations**      | CRUD        | CRUD  | CRUD             | CR\*\*     | R          | R        | R      |
| **Gestion des notes**            | CRUD        | CRUD  | CRUD             | CRUD\*\*   | R          | R        | R      |
| **Gestion des bulletins**        | CRUD        | CRUD  | CRUD             | R          | CR         | R        | R      |
| **Gestion des présences**        | CRUD        | CRUD  | CRUD             | CRUD\*\*   | CRUD       | R        | R      |
| **Gestion des annonces**         | CRUD        | CRUD  | CRUD             | CRUD\*\*   | CRUD       | R        | R      |
| **Statistiques**                 | CRUD        | CRUD  | CRUD             | R\*\*      | R          | -        | -      |

Légende:

-   CRUD : Création, Lecture, Mise à jour, Suppression
-   CR : Création et Lecture
-   R : Lecture seulement
-   -   : Aucun accès
-   -   : Accès limité à ses propres données
-   \*\* : Accès limité à ses propres classes/cours

## Implémentation technique

Le système utilisera Spatie Laravel Permission, une bibliothèque populaire pour la gestion des rôles et permissions dans Laravel. Cette bibliothèque offre:

1. **Gestion des rôles** : Définition et attribution de rôles aux utilisateurs
2. **Gestion des permissions** : Définition des permissions et attribution aux rôles
3. **Vérification des permissions** : Middleware pour la vérification des permissions
4. **Interfaces de gestion** : Interfaces pour gérer les rôles et permissions

## Middlewares de contrôle d'accès

Le système utilisera plusieurs middlewares pour sécuriser les routes:

1. `role`: Vérifie si l'utilisateur a un rôle spécifique
2. `permission`: Vérifie si l'utilisateur a une permission spécifique
3. `role_or_permission`: Vérifie si l'utilisateur a un rôle OU une permission

## Seeding initial

Le système sera initialisé avec les rôles et permissions par défaut, ainsi qu'un compte Super Administrateur pour la configuration initiale.

## Interface de gestion

Une interface complète sera développée pour:

1. Créer et gérer les rôles
2. Définir les permissions pour chaque rôle
3. Assigner des rôles aux utilisateurs
4. Visualiser les permissions effectives
