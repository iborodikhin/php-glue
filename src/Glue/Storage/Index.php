<?php
/**
 * User: majesty
 * Date: 14.07.13
 */

namespace Glue\Storage;


class Index extends AbstractFile
{
    /**
     * File extension
     *
     * @var string
     */
    protected $extension = 'idx';

    /**
     * Save meta-data to index
     *
     * @param $key
     * @param $offset
     * @param $length
     * @param string $meta
     * @return \FALSE|int
     */
    public function save($key, $offset, $length, $meta = "")
    {
        $data = sprintf(
            "%s\t%s\t%s\t%s" . PHP_EOL,
            $key,
            $offset,
            $length,
            $meta
        );

        $fh = $this->open();
        flock($fh, LOCK_EX);
        fseek($fh, $this->size());
        $result = fputs($fh, $data);
        flock($fh, LOCK_UN);

        return $result;
    }

    /**
     * Reads meta-data from index
     *
     * @param $key
     * @return array|bool
     */
    public function read($key)
    {
        return $this->findOrDelete($key, false);
    }

    /**
     * Deletes meta-data from index
     *
     * @param $key
     * @return bool
     */
    public function delete($key)
    {
        return $this->findOrDelete($key, true);
    }

    /**
     * Finds or deletes meta-data in index
     *
     * @param $key
     * @param bool $delete
     * @return array|bool
     */
    protected function findOrDelete($key, $delete = false)
    {
        $found = false;

        $fh = $this->open();
        flock($fh, ($delete ? LOCK_EX : LOCK_SH));
        fseek($fh, 0);
        do {
            $string = fgets($fh);
            if (false !== strpos($string, $key)) {
                $found = true;
            }
        } while (!feof($fh) && !$found);

        if ($found) {
            $result = explode("\t", trim($string));
            $hash   = array_shift($result);
            if (trim($hash) == trim($key)) {
                if (!$delete) {
                    flock($fh, LOCK_UN);
                    return $result;
                } else {
                    fseek($fh, 0 - strlen($string), SEEK_CUR);
                    fputs($fh, str_repeat(chr(0), strlen(trim($string))));
                    flock($fh, LOCK_UN);
                    return $result;
                }
            }
        }

        flock($fh, LOCK_UN);
        return false;
    }
}