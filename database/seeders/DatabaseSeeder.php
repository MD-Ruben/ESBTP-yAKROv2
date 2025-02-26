<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Appeler les seeders individuels
        $this->call([
            UserSeeder::class,
        ]);
    }
}
