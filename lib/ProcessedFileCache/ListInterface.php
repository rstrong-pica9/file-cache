<?php

namespace Pica9\ProcessedFileCache;

interface ListInterface
{
    /*
     * This function assumes $id is already in the list and should move it to the top
     * @return void
     */
    public function refreshId($id);

    /*
     * This function assumes the $id is not in the list and adds it to the top
     * @return void
     */
    public function storeId($id);

    /*
     * This function removes the id at the end of the list and returns it.
     * @return string The id of the removed item. FALSE if the list is empty
     */
    public function removeOldest();
}
