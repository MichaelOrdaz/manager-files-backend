<?php
namespace App\Helpers;

use App\Models\Document;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use League\Flysystem\Util;

class Dixa
{
    const FOLDER = 'Carpeta';
    const FILE = 'Archivo';
    const MB = 1024;
    const PATH_FILES = 'files';
    const STORAGE_ROOT_PATH = 'app/public/' . self::PATH_FILES;
    const DEFAULT_PERMISSION = 0755;

    const ACTION_CREATED = 'Creado';
    const ACTION_DELETED = 'Eliminado';
    const ACTION_UPDATED = 'Modificado';
    const ACTION_SHARED = 'Compartido';

    const HISTORY_ACTIONS = [
        self::ACTION_CREATED,
        self::ACTION_UPDATED,
        self::ACTION_DELETED,
        self::ACTION_SHARED
    ];

    const PERMISSION_TO_READ_SHARED_DOCUMENT = 'Lectura';
    const PERMISSION_TO_WRITE_SHARED_DOCUMENT = 'Escritura';

    const SHARE_DOCUMENT_PERMISSIONS = [
        self::PERMISSION_TO_READ_SHARED_DOCUMENT,
        self::PERMISSION_TO_WRITE_SHARED_DOCUMENT
    ];

    const SPANISH_ROLES = [
        'Analyst' => 'Analista',
        'Admin' => 'Administrador',
        'Head of Department' => 'Jefe de Departamento',
    ];

    public static function useFolder($path): bool
    {
        $isDirectory = File::isDirectory($path);
        if ($isDirectory) {
            return $isDirectory;
        }
        return File::makeDirectory($path, self::DEFAULT_PERMISSION, true);
    }

    public static function storageRootPath($path = ''): string
    {
        $path = Util::normalizePath($path);
        if ($path) {
            return storage_path(self::STORAGE_ROOT_PATH . DIRECTORY_SEPARATOR . $path);
        }
        return storage_path(self::STORAGE_ROOT_PATH);
    }

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

    public static function getChildrenProperty($children, $prop = null)
    {
        $data = collect([]);
        foreach ($children as $child) {
            $data->push($prop ? $child->{$prop} : $child);
            if ($child->children instanceof Collection) {
                $data = $data->merge(self::getChildrenProperty($child->children, $prop));
            }
        }
        return $data;
    }
}