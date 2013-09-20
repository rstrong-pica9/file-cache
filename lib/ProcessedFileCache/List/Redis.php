<?php

namespace Pica9\ProcessedFileCache\List;

class Redis
{
    private $redis;
    private $listKey = 'processed-file-cache';

    public function __construct($connection)
    {
        if (is_object($connection) && get_class($connection) == "Predis\Client") {
            $this->redis = $connection;
        } elseif (is_array($connection)) {
            $this->redis = new Predis\Client($connection);
        } else {
            throw new \Exception('Invalid value for parameter: $connection');
        }
    }

    public function promoteId($id)
    {
        $this->redis->lrem($this->listKey, 0, $id);
        $this->storeId($id);
    }

    public function storeId($id)
    {
        $this->redis->lpush($this->listKey, $id);
    }

    public function removeOldest()
    {
        return $this->redis->rpop($this->listKey);
    }
}
