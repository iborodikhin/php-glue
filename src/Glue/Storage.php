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
     * Storage key
     *
     * @var null|string
     */
    protected $key   = null;

    /**
     * Storage path
     *
     * @var null|string
     */
    protected $path  = null;

    /**
     * Public constructor
     *
     * @param $key
     * @param $path
     */
    public function __construct($key, $path)
    {
        $this->key   = $key;
        $this->path  = $path;

        $this->index = new Storage\Index($this->getKeyPath());
        $this->blob  = new Storage\Blob($this->getKeyPath());
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
     * @return bool
     */
    public function delete($name)
    {
        $key = $this->getKey($name);
        $result = $this->index->delete($key);
        if (is_array($result) && count($result) == 2) {
            return $this->blob->delete($result[0], $result[1]);
        }
        return false;
    }

    /**
     * Compacts storage
     *
     * @return bool
     */
    public function compact()
    {
        $result = true;

        // TODO: Implement

        return $result;
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

    /**
     * Returns path for storage path and key
     *
     * @return string
     */
    protected function getKeyPath()
    {
        return $this->path . DIRECTORY_SEPARATOR . $this->key;
    }
}