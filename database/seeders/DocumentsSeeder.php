<?php

namespace Database\Seeders;

use App\Helpers\Dixa;
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
        $typeFolder = $documentType->where('name', Dixa::FOLDER)->first();
        $typeFile = $documentType->where('name', Dixa::FILE)->first();

        //creo 10 documentos (carpetas y archivos) de cada jefe de departamento en el nivel raiz
        User::role('head of department')->get()
        ->each(function ($user) use (
            $documentType,
            $typeFolder,
            $typeFile
        ) {
            //level root
            $documentsRoot = Document::factory()->count(10)
            ->state(new Sequence(
                fn ($sequence) => [
                    'type_id' => $documentType->random()->id
                ]
            ))
            ->for($user->department)
            ->for($user, 'creator')
            ->create();
            
            $foldersRoot = $documentsRoot->where('type_id', $typeFolder->id);
            
            //level 2
            $foldersRoot->each(function ($folderRoot) use (
                $documentType,
                $typeFolder,
                $typeFile, 
                $user
            ) {
                $quantity = rand(3, 5);
                $documentsSecondLevel = Document::factory()->count($quantity)
                ->state(new Sequence(
                    fn ($sequence) => [
                        'type_id' => $documentType->random()->id
                    ]
                ))
                ->for($user->department)
                ->for($user, 'creator')
                ->for($folderRoot, 'parent')
                ->create();

                //level 3
                $foldersSecondLevel = $documentsSecondLevel->where('type_id', $typeFolder->id);

                $foldersSecondLevel->each(function ($folderSecondLevel) use (
                    $documentType,
                    $typeFolder,
                    $typeFile, 
                    $user
                ) {
                    $quantity = rand(3, 5);
                    $documentsThreeLevel = Document::factory()->count($quantity)
                    ->state(new Sequence(
                        fn ($sequence) => [
                            'type_id' => $documentType->random()->id
                        ]
                    ))
                    ->for($user->department)
                    ->for($user, 'creator')
                    ->for($folderSecondLevel, 'parent')
                    ->create();
                    
                    //level 4
                    $foldersThreeLevel = $documentsThreeLevel->where('type_id', $typeFolder->id);

                    $foldersThreeLevel->each(function ($folderThreeLevel) use (
                        $documentType,
                        $typeFolder,
                        $typeFile, 
                        $user
                    ) {
                        $quantity = rand(2, 4);
                        Document::factory()->count($quantity)
                        ->for($typeFile, 'type')
                        ->for($user->department)
                        ->for($user, 'creator')
                        ->for($folderThreeLevel, 'parent')
                        ->create();
                    });
                
                });
            });
        });
    }
}
