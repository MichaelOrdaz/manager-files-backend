<?php

namespace Database\Seeders;

use App\Helpers\Dixa;
use App\Models\DocumentType;
use Illuminate\Database\Seeder;

class DocumentTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DocumentType::create([
            'name' => Dixa::FOLDER
        ]);

        DocumentType::create([
            'name' => Dixa::FILE
        ]);
    }
}
