<?php

namespace Pica9\ProcessedFileCache;

interface ListInterface
{
    public function promoteFile($id);
    public function storeFile($id, $fileData);
    public function getFile($id);
    public function removeOldest();
}
