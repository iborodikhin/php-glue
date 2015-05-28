<?php
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
     * @param  string $data
     * @return array
     */
    public function save($data)
    {
        $result = false;
        $fh = $this->open();
        flock($fh, LOCK_EX);
        $offset = $this->size();

        if (-1 !== fseek($fh, $offset)) {
            $result = fwrite($fh, $data);
        }

        flock($fh, LOCK_UN);

        if ($result === false) {
            return false;
        }

        return array($offset, $result);
    }

    /**
     * Reads data from BLOB.
     *
     * @param  integer $offset
     * @param  integer $length
     * @return string
     */
    public function read($offset, $length)
    {
        $result = false;
        $fh = $this->open();
        flock($fh, LOCK_SH);

        if (-1 !== fseek($fh, $offset)) {
            $result = fread($fh, $length);
        }

        flock($fh, LOCK_UN);

        return $result;
    }

    /**
     * Deletes data from BLOB
     *
     * @param  integer $offset
     * @param  integer $length
     * @return boolean
     */
    public function delete($offset, $length)
    {
        $result = false;
        $fh = $this->open();
        flock($fh, LOCK_EX);

        if (-1 !== fseek($fh, $offset)) {
            $result = fwrite($fh, str_repeat(chr(0), $length));
        }

        flock($fh, LOCK_UN);

        if ($result === false) {
            return false;
        }

        return true;
    }
}
