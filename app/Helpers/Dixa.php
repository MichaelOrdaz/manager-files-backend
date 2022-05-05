<?php
namespace App\Helpers;

use App\Models\Document;

class Dixa
{
    const FOLDER = 'Carpeta';
    const FILE = 'Archivo';

    public static function getPath(Document $document): string
    {
        $sections = [
            $document->name
        ];
        while ($document->parent !== null) {
            $document = $document->parent;
            $sections[] = $document->name;
        }
        $sections = array_reverse($sections);
        $sections = implode('/', $sections);
        $sections .= '/';
        return $sections;
    }
}