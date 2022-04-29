<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class DocumentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $documentType = DocumentType::all();

        //creo 10 documentos (carpetas y archivos) de cada jefe de departamento en el nivel raiz
        User::role('head of department')->get()
        ->each(function ($user) use ($documentType) {
            Document::factory()->count(10)
            ->state(new Sequence(
                fn ($sequence) => [
                    'type_id' => $documentType->random()->id
                ]
            ))
            ->for($user->department)
            ->for($user, 'creator')
            ->create();

            $typeFolder = $documentType->where('name', 'Carpeta')->first();
            $typeFile = $documentType->where('name', 'Archivo')->first();

            $folders = Document::where('type_id', $typeFolder->id)
            ->where('department_id', $user->department->id)
            ->get();

            $folders->each(function ($folder) use ($typeFile, $user) {
                $quantity = rand(2, 4);
                Document::factory()->count($quantity)
                ->for($typeFile, 'type')
                ->for($user->department)
                ->for($user, 'creator')
                ->for($folder, 'parent')
                ->create();
            });
        });
    }
}
