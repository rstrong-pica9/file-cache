<?php

namespace Pica\ProcessedFile;

class Cache
{
    private static $db;
    private static $maxCacheSize;
    private static $relativeFileRoot;

    public function __construct(ListInterface $db)
    {
        $this->db = $db;
    }

    public function setMaxCacheSize($megabytes)
    {
        self::$maxCacheSize = $megabytes;
    }

    public function static getFile($uniqueId)
    {
        if (($file = self::fileIsCached($uniqueId) !== false) {
            self::$db->promoteFile($uniqueId);
            return file_get_contents($file);
        }

        return false;
    }

    public function static storeFile($uniqueId, $fileData)
    {
        file_put_contents(self::$relativeFileRoot . $uniqueId, $fileData);
        self::$db->storeFile($uniqueId, $fileData);
    }

    public function static fileIsCached($uniqueId)
    {
        $fileLocation = self::$db->getFile($uniqueId);
        if (is_null($fileLocation)) {
            return false;
        }
        if (is_file(self::$relativeFileRoot . $fileLocation)) {
            return self::$relativeFileRoot . $fileLocation;
        }
        return false;
    }

    public function static regulateCacheSize()
    {
        while (self::getDirectorySize() > self::$maxCacheSize) {
            self::removeOldestFile();
        }
    }

    protected function static getDirectorySize($dir = null)
    {
        if (is_null($dir)) {
            $dir = self::$relativeFileRoot;
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
        $file = self::$db->removeOldest();
        unlink(self::$relativeFileRoot . $file);
    }

}
