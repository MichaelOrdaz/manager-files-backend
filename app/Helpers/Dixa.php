<?php
namespace App\Helpers;

use App\Models\Document;
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
}