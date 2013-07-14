<?php
/**
 * User: majesty
 * Date: 14.07.13
 */

namespace Glue;


class Storage
{
    /**
     * Index instance
     *
     * @var Storage\Index|null
     */
    protected $index = null;

    /**
     * Blob instance
     *
     * @var Storage\Blob|null
     */
    protected $blob  = null;

    /**
     * Public constructor
     *
     * @param $key
     * @param $path
     */
    public function __construct($key, $path)
    {
        $this->index = new Storage\Index($path . DIRECTORY_SEPARATOR . $key);
        $this->blob  = new Storage\Blob($path . DIRECTORY_SEPARATOR . $key);
    }

    /**
     * Saves item to storage
     *
     * @param $name
     * @param $value
     * @return bool|\FALSE|int
     */
    public function save($name, $value)
    {
        $key   = $this->getKey($name);
        $index = $this->blob->save($value);

        if ($index === false) {
            return false;
        }

        return $this->index->save($key, $index[0], $index[1]);
    }

    /**
     * Reads item from storage
     *
     * @param $name
     * @return bool|string
     */
    public function read($name)
    {
        $key   = $this->getKey($name);
        $index = $this->index->read($key);
        if (false === $index) {
            return false;
        }

        $offset = $index[0];
        $length = $index[1];
        return $this->blob->read($offset, $length);
    }

    /**
     * Deletes item from storage
     *
     * @param $name
     */
    public function delete($name)
    {
        $key = $this->getKey($name);
        $this->index->delete($key);
    }

    /**
     * Returns hash of name
     *
     * @param $name
     * @return string
     */
    protected function getKey($name)
    {
        return sha1($name);
    }
}