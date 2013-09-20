<?php

namespace Pica\ProcessedFile;

class Cache
{
    private static $list;
    private static $maxCacheSize;
    private static $relativeFileRoot;

    public function __construct(ListInterface $list = null)
    {
        if (!is_null($list)) {
            self::setListBackend($list);
        }
    }

    public function setListBackend(ListInterface $list)
    {
        self::$list = $list;
    }

    public function getListBackend()
    {
        return self::$list;
    }

    public function setMaxCacheSize($megabytes)
    {
        self::$maxCacheSize = $megabytes;
    }

    public function getMaxCacheSize()
    {
        return self::$maxCacheSize;
    }

    public function setRelativeFileRoot($path)
    {
        if (!is_dir($path)) {
            mkdir($path, 0775, true);
        }
        $path = realpath($path);
        if (!$path) {
            throw new Exception('Error setting up file path');
        }
        self::$relativeFileRoot = $path;
    }

    public function getFileRoot()
    {
        return self::$relativeFileRoot;
    }

    public function static getFile($uniqueId)
    {
        if (self::fileIsCached($uniqueId)) {
            self::getListBackend()->refreshId($uniqueId);
            return file_get_contents(self::getFileRoot() . $file);
        }

        return false;
    }

    public function static storeFile($uniqueId, $fileData)
    {
        file_put_contents(self::getFileRoot() . $uniqueId, $fileData);
        self::getListBackend()->storeId($uniqueId);
    }

    public function static fileIsCached($uniqueId)
    {
        if (is_file(self::getFileRoot() . $fileLocation)) {
            return true;
        }
        return false;
    }

    public function static regulateCacheSize()
    {
        while (self::getDirectorySize() > self::getMaxCacheSize()) {
            if (!self::removeOldestFile()) {
                throw new \Exception('The list is empty, but the cache is still over the limit!');
            }
        }
    }

    protected function static getDirectorySize($dir = null)
    {
        if (is_null($dir)) {
            $dir = self::getFileRoot();
        }
        $size = 0;
        $files = scandir($dir);
        foreach($files as $key => $filename) {
            if(!in_array($filename, array("..", "."))) {
                if (is_dir($dir . "/" . $filename)) {
                    $size += self::getDirectorySize($dir . "/" . $filename);
                } elseif (is_file($dir . "/" . $filename)) {
                    $size += filesize($dir . "/" . $filename);
                }
            }
        }
        //convert to megabytes
        return $size / (1024 * 1024);
    }

    public static function removeOldestFile()
    {
        $id = self::getListBackend()->removeOldest();
        unlink(self::getFileRoot() . $id);
    }

}
