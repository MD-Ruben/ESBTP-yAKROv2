<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\ESBTPCategoriePaiement;

class AddCategorieIdToEsbtpPaiementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('esbtp_paiements', function (Blueprint $table) {
            $table->foreignId('categorie_id')->nullable()->after('type_paiement')->constrained('esbtp_categorie_paiements')->onDelete('set null');
        });

        // Mettre à jour les enregistrements existants
        $this->updateExistingRecords();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('esbtp_paiements', function (Blueprint $table) {
            $table->dropForeign(['categorie_id']);
            $table->dropColumn('categorie_id');
        });
    }

    /**
     * Mettre à jour les enregistrements existants avec la catégorie correspondante.
     */
    private function updateExistingRecords()
    {
        // Attendre que les catégories soient créées
        sleep(1);

        // Mapping des types de paiement actuels vers les nouveaux codes de catégorie
        $mappingTypes = [
            'Frais d\'inscription' => 'INSCRIPTION',
            'Frais de scolarité' => 'SCOLARITE',
            'Mensualité' => 'SCOLARITE',
            'Trimestriel' => 'SCOLARITE',
            'Semestriel' => 'SCOLARITE',
            'Frais d\'examen' => 'EXAMEN',
            'Frais de diplôme' => 'DIPLOME',
            'Autre' => 'AUTRES',
        ];

        // Récupérer toutes les catégories de paiement
        $categories = DB::table('esbtp_categorie_paiements')->get()->keyBy('code');

        // Mettre à jour chaque paiement
        foreach ($mappingTypes as $typePaiement => $codeCategorie) {
            if (isset($categories[$codeCategorie])) {
                DB::table('esbtp_paiements')
                    ->where('type_paiement', $typePaiement)
                    ->update(['categorie_id' => $categories[$codeCategorie]->id]);
            }
        }

        // Pour les types non mappés, utiliser la catégorie "Autres recettes"
        if (isset($categories['AUTRES'])) {
            DB::table('esbtp_paiements')
                ->whereNull('categorie_id')
                ->update(['categorie_id' => $categories['AUTRES']->id]);
        }
    }
}
