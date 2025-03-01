<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ESBTPFiliereSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Créer les filières principales
        $genieCivil = DB::table('esbtp_filieres')->insertGetId([
            'name' => 'Génie Civil',
            'code' => 'GC',
            'description' => 'Formation en génie civil et construction',
            'is_active' => true,
            'parent_id' => null,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $mineGeoPetro = DB::table('esbtp_filieres')->insertGetId([
            'name' => 'Mine - Géologie - Pétrole',
            'code' => 'MGP',
            'description' => 'Formation en exploitation minière, géologie et pétrole',
            'is_active' => true,
            'parent_id' => null,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Créer les sous-filières pour Génie Civil
        $sousFilieresGC = [
            [
                'name' => 'Génie Civil option BATIMENT',
                'code' => 'GC-BAT',
                'description' => 'Spécialisation en construction de bâtiments',
                'is_active' => true,
                'parent_id' => $genieCivil,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Génie Civil option TRAVAUX PUBLICS',
                'code' => 'GC-TP',
                'description' => 'Spécialisation en travaux publics et infrastructures',
                'is_active' => true,
                'parent_id' => $genieCivil,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Génie Civil option URBANISME',
                'code' => 'GC-URB',
                'description' => 'Spécialisation en aménagement urbain',
                'is_active' => true,
                'parent_id' => $genieCivil,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Génie Civil option GEOMETRE-TOPOGRAPHE',
                'code' => 'GC-GT',
                'description' => 'Spécialisation en géométrie et topographie',
                'is_active' => true,
                'parent_id' => $genieCivil,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        DB::table('esbtp_filieres')->insert($sousFilieresGC);

        // Log les informations
        \Log::info('Seeders de filières ESBTP créés avec succès');
    }
}
