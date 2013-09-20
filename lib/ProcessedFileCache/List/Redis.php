<?php

namespace Pica9\ProcessedFileCache\List;

class Redis
{
    private $redis;

    public function __construct($connection)
    {
        if (is_object($connection) && get_class($connection) == "Predis\Client") {
            $this->redis = $connection;
        } elseif (is_array($connection)) {
            $this->redis = new Predis\Client($connection);
        }
    }

    public function promoteFile($id)
    {

    }

    public function storeFile($id, $fileData)
    {

    }

    public function getFile($id)
    {

    }

    public function removeOldest()
    {

    }
}
