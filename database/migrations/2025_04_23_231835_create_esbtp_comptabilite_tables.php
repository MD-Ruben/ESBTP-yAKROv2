<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEsbtpComptabiliteTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Table de configuration de la comptabilité
        Schema::create('esbtp_comptabilite_configurations', function (Blueprint $table) {
            $table->id();
            $table->string('cle');
            $table->text('valeur')->nullable();
            $table->text('description')->nullable();
            $table->boolean('est_actif')->default(true);
            $table->timestamps();
        });

        // Table des frais de scolarité
        Schema::create('esbtp_frais_scolarite', function (Blueprint $table) {
            $table->id();
            $table->foreignId('filiere_id')->nullable()->constrained('esbtp_filieres')->onDelete('cascade');
            $table->foreignId('niveau_id')->nullable()->constrained('esbtp_niveau_etudes')->onDelete('cascade');
            $table->foreignId('annee_universitaire_id')->constrained('esbtp_annee_universitaires')->onDelete('cascade');
            $table->decimal('montant_total', 10, 2);
            $table->decimal('frais_inscription', 10, 2)->default(0);
            $table->decimal('frais_mensuel', 10, 2)->default(0);
            $table->decimal('frais_trimestriel', 10, 2)->default(0);
            $table->decimal('frais_semestriel', 10, 2)->default(0);
            $table->decimal('frais_annuel', 10, 2)->default(0);
            $table->integer('nombre_echeances')->default(1);
            $table->text('details')->nullable();
            $table->boolean('est_actif')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            // Index composites
            $table->unique(['filiere_id', 'niveau_id', 'annee_universitaire_id'], 'frais_scolarite_unique');
        });

        // Table des paiements étudiants
        Schema::create('esbtp_paiements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('etudiant_id')->constrained('esbtp_etudiants')->onDelete('cascade');
            $table->foreignId('inscription_id')->nullable()->constrained('esbtp_inscriptions')->onDelete('set null');
            $table->foreignId('annee_universitaire_id')->constrained('esbtp_annee_universitaires');
            $table->string('type_paiement'); // inscription, scolarité, examen, etc.
            $table->decimal('montant', 10, 2);
            $table->string('reference_paiement')->unique();
            $table->string('mode_paiement'); // espèces, chèque, virement, mobile money, etc.
            $table->string('numero_transaction')->nullable();
            $table->date('date_paiement');
            $table->date('date_echeance')->nullable();
            $table->text('description')->nullable();
            $table->string('statut')->default('completé'); // en attente, completé, annulé, remboursé, etc.
            $table->foreignId('createur_id')->nullable()->constrained('users');
            $table->foreignId('validateur_id')->nullable()->constrained('users');
            $table->timestamp('date_validation')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Table des factures
        Schema::create('esbtp_factures', function (Blueprint $table) {
            $table->id();
            $table->string('numero_facture')->unique();
            $table->foreignId('etudiant_id')->constrained('esbtp_etudiants')->onDelete('cascade');
            $table->foreignId('annee_universitaire_id')->constrained('esbtp_annee_universitaires');
            $table->date('date_emission');
            $table->date('date_echeance');
            $table->decimal('montant_total', 10, 2);
            $table->decimal('montant_regle', 10, 2)->default(0);
            $table->string('statut')->default('émise'); // émise, partiellement réglée, réglée, annulée
            $table->text('details')->nullable();
            $table->text('notes')->nullable();
            $table->string('path_facture')->nullable(); // Chemin vers le fichier PDF généré
            $table->foreignId('createur_id')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });

        // Table des détails de facture
        Schema::create('esbtp_facture_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facture_id')->constrained('esbtp_factures')->onDelete('cascade');
            $table->string('designation');
            $table->text('description')->nullable();
            $table->decimal('montant', 10, 2);
            $table->integer('quantite')->default(1);
            $table->decimal('total_ligne', 10, 2);
            $table->timestamps();
        });

        // Table des catégories de dépenses
        Schema::create('esbtp_categories_depenses', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->foreignId('parent_id')->nullable()->references('id')->on('esbtp_categories_depenses');
            $table->boolean('est_actif')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // Table des dépenses
        Schema::create('esbtp_depenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categorie_id')->constrained('esbtp_categories_depenses');
            $table->string('reference')->unique();
            $table->string('libelle');
            $table->text('description')->nullable();
            $table->decimal('montant', 10, 2);
            $table->date('date_depense');
            $table->string('mode_paiement');
            $table->string('numero_transaction')->nullable();
            $table->foreignId('fournisseur_id')->nullable()->constrained('esbtp_fournisseurs');
            $table->string('statut')->default('validée'); // en attente, validée, annulée
            $table->foreignId('createur_id')->nullable()->constrained('users');
            $table->foreignId('validateur_id')->nullable()->constrained('users');
            $table->timestamp('date_validation')->nullable();
            $table->string('path_justificatif')->nullable(); // Chemin vers le document justificatif
            $table->text('notes_internes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Table des fournisseurs
        Schema::create('esbtp_fournisseurs', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('nom');
            $table->string('type')->nullable(); // personne physique, entreprise
            $table->string('adresse')->nullable();
            $table->string('ville')->nullable();
            $table->string('pays')->default('Côte d\'Ivoire');
            $table->string('telephone')->nullable();
            $table->string('email')->nullable();
            $table->string('site_web')->nullable();
            $table->string('numero_fiscal')->nullable();
            $table->string('compte_bancaire')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('est_actif')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // Table des bourses
        Schema::create('esbtp_bourses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('etudiant_id')->constrained('esbtp_etudiants')->onDelete('cascade');
            $table->foreignId('annee_universitaire_id')->constrained('esbtp_annee_universitaires');
            $table->string('type_bourse'); // complète, partielle, mérite, sociale
            $table->decimal('montant', 10, 2)->nullable();
            $table->decimal('pourcentage', 5, 2)->nullable(); // pourcentage de réduction
            $table->date('date_debut');
            $table->date('date_fin')->nullable();
            $table->string('statut')->default('active'); // active, suspendue, terminée
            $table->string('organisme_financeur')->nullable();
            $table->text('conditions')->nullable();
            $table->text('commentaires')->nullable();
            $table->foreignId('createur_id')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });

        // Table des salaires
        Schema::create('esbtp_salaires', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('annee_universitaire_id')->constrained('esbtp_annee_universitaires');
            $table->integer('mois'); // 1-12
            $table->integer('annee');
            $table->decimal('salaire_base', 10, 2);
            $table->decimal('heures_supplementaires', 10, 2)->default(0);
            $table->decimal('primes', 10, 2)->default(0);
            $table->decimal('indemnites', 10, 2)->default(0);
            $table->decimal('retenues', 10, 2)->default(0);
            $table->decimal('charges_sociales', 10, 2)->default(0);
            $table->decimal('impots', 10, 2)->default(0);
            $table->decimal('montant_net', 10, 2);
            $table->date('date_paiement')->nullable();
            $table->string('reference_paiement')->nullable();
            $table->string('statut')->default('calculé'); // calculé, validé, payé
            $table->string('path_bulletin')->nullable(); // Chemin vers le bulletin de paie
            $table->foreignId('createur_id')->nullable()->constrained('users');
            $table->foreignId('validateur_id')->nullable()->constrained('users');
            $table->timestamp('date_validation')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Index unique pour éviter les doublons
            $table->unique(['user_id', 'mois', 'annee'], 'salaire_unique');
        });

        // Table des transactions financières (journal)
        Schema::create('esbtp_transactions_financieres', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // revenus, dépenses, ajustement
            $table->morphs('transactionable'); // Relation polymorphique (paiement, dépense, etc.)
            $table->decimal('montant', 10, 2);
            $table->string('sens'); // débit, crédit
            $table->string('categorie');
            $table->string('reference');
            $table->date('date_transaction');
            $table->text('description')->nullable();
            $table->foreignId('compte_id')->nullable(); // Pour intégration future avec plan comptable
            $table->foreignId('createur_id')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('esbtp_transactions_financieres');
        Schema::dropIfExists('esbtp_salaires');
        Schema::dropIfExists('esbtp_bourses');
        Schema::dropIfExists('esbtp_depenses');
        Schema::dropIfExists('esbtp_fournisseurs');
        Schema::dropIfExists('esbtp_categories_depenses');
        Schema::dropIfExists('esbtp_facture_details');
        Schema::dropIfExists('esbtp_factures');
        Schema::dropIfExists('esbtp_paiements');
        Schema::dropIfExists('esbtp_frais_scolarite');
        Schema::dropIfExists('esbtp_comptabilite_configurations');
    }
}
