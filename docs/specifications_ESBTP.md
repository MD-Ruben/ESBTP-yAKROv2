# Spécifications fonctionnelles - Application ESBTP

Ce document détaille toutes les fonctionnalités attendues de l'application de gestion ESBTP.

## 1. Gestion des classes

-   **Contenu**: Filière, formation et niveau d'étude
-   **Processus de création**:
    1. Choix de la filière
    2. Choix du type de formation
    3. Choix du niveau d'étude (1ère année ou 2ème année)
    4. Création de la classe
-   **Associations**:
    -   Étudiants rattachés à la classe
    -   Emploi du temps associé à la classe

## 2. Gestion des étudiants

-   **Informations personnelles**:
    -   Nom, prénom(s)
    -   Date de naissance (format JJ/MM/AAAA)
    -   E-mail
    -   Téléphone (format: +225 XX XX XXX XXX)
    -   Genre (homme/femme)
    -   Adresse (Ville, Commune)
    -   Photo de profil
    -   Date d'admission
    -   Matricule
    -   Mot de passe
    -   Numéro d'inscription (ID de l'étudiant)
-   **Gestion des parents**:
    -   Possibilité d'ajouter jusqu'à deux parents
    -   Option de choisir des parents existants
    -   Option d'ajouter de nouveaux parents
-   **Automatisation**:
    -   Assignation automatique à une classe

## 3. Gestion des matières

-   **Structure**:
    -   Rattachement à un niveau d'étude (BTS 1ère année, BTS 2ème année)
    -   Rattachement à une formation (générale ou technologique et professionnelle)
    -   Rattachement à une filière
-   **Groupes de matières**:
    -   Format: [Niveau d'étude]_[Filière]_[Formation]\_[Année]
    -   Exemple: BTS_mine-géologie-pétrole_formationgeneral_1annee
-   **Gestion des coefficients**:
    -   Interface pour ajouter des coefficients aux matières
    -   Utilisation pour le calcul des moyennes dans les bulletins

## 4. Gestion des emplois du temps (timetable)

-   Interface CRUD complète
-   Création d'un emploi du temps lié à une classe spécifique

## 5. Gestion des évaluations

-   **Types d'évaluation**:
    -   Types prédéfinis: examen, quiz, devoir
    -   Possibilité d'ajouter de nouveaux types
-   Interface CRUD complète

## 6. Gestion des notes

-   Rattachement à une évaluation
-   Rattachement à une classe (filière, formation, niveau d'étude)
-   Rattachement à un étudiant
-   Interface CRUD complète

## 7. Gestion des bulletins

-   **Composants**:
    -   Classe (filière, formation, niveau d'étude)
    -   Notes
    -   Évaluations
    -   Étudiant
-   Calcul automatique des moyennes
-   Interface CRUD complète

## 8. Gestion des annonces

-   **Contenu**:
    -   Objet
    -   Corps du message
    -   Destinataires
-   **Gestion des destinataires**:
    -   Tous les étudiants
    -   Une classe entière (tous les étudiants de cette classe)
    -   Des étudiants spécifiques
-   **Interface de sélection**:
    -   Modal multichoix pour sélectionner les destinataires
    -   Filtres (par classe, filière, formation, niveau d'étude)
    -   Bouton "select all"
-   Interface CRUD complète

## 9. Gestion des présences (attendance)

-   Interface pour marquer les présences des étudiants aux cours
-   Filtrage par classe et par période
-   Génération de rapports
-   Interface CRUD complète
