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
     * @return \FALSE|int
     */
    public function save($key, $offset, $length)
    {
        $data = sprintf(
            "%s\t%s\t%s" . PHP_EOL,
            $key,
            $offset,
            $length
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
        $found = false;

        $fh = $this->open();
        flock($fh, LOCK_SH);
        fseek($fh, 0);
        do {
            $string = fgets($fh);
            if (false !== strpos($string, $key)) {
                $found = true;
            }
        } while (!feof($fh) && !$found);
        flock($fh, LOCK_UN);

        if ($found) {
            $result = explode("\t", trim($string));
            $hash   = array_shift($result);
            if (trim($hash) == trim($key)) {
                return $result;
            }
        }

        return false;
    }

    public function delete($key)
    {
        $fh = $this->open();
        /**
         * TODO: Implement
         */
    }
}