<?php
/**
 * User: majesty
 * Date: 14.07.13
 */

namespace Glue\Storage;


class Blob extends AbstractFile
{
    /**
     * File extension
     *
     * @var string
     */
    protected $extension = 'dat';

    /**
     * Writes data to BLOB
     *
     * @param $data
     * @return array
     */
    protected function save($data)
    {
        $fh = $this->open();
        flock($fh, LOCK_EX);
        $offset = $this->size();
        fseek($fh, $offset);
        $result = fwrite($fh, $data);
        flock($fh, LOCK_UN);

        return array($offset, $result);
    }

    /**
     * Reads data from BLOB.
     *
     * @param $offset
     * @param $length
     * @return string
     */
    protected function readData($offset, $length)
    {
        $fh = $this->open();
        flock($fh, LOCK_SH);
        fseek($fh, $offset);
        $result = fread($fh, $length);
        flock($fh, LOCK_UN);

        return $result;
    }

}