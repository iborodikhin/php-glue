<?php
/**
 * User: majesty
 * Date: 14.07.13
 */

namespace Glue;


class Glue
{
    /**
     * Directory in which data stored
     *
     * @var string
     */
    protected $path   = __DIR__;

    /**
     * How many BLOBs to create — 16^$levels
     *
     * @var int
     */
    protected $levels = 2;

    /**
     * Public constructor.
     * Creates Glue instance.
     *
     * @param string $path
     * @param int $levels
     */
    public function __construct($path = __DIR__, $levels = 2)
    {
        $this->path   = $path;
        $this->levels = $levels;
    }

    /**
     * Save data to storage.
     *
     * @param $name
     * @param $value
     * @return bool
     */
    public function save($name, $value)
    {
        $key     = $this->key2storage($name);
        $storage = new \Glue\Storage($key, $this->path);
        $result  = $storage->save($name, $value);

        return $result;
    }

    /**
     * Read data from storage.
     *
     * @param $name
     * @return string
     */
    public function read($name)
    {
        $key     = $this->key2storage($name);
        $storage = new \Glue\Storage($key, $this->path);
        $result  = $storage->read($name);

        return $result;
    }

    /**
     * Remove data from storage.
     *
     * @param $name
     * @return bool
     */
    public function delete($name)
    {
        $key     = $this->key2storage($name);
        $storage = new \Glue\Storage($key, $this->path);
        $result  = $storage->delete($name);

        return $result;
    }

    /**
     * Compact storage.
     *
     * @return bool
     */
    public function compact()
    {
        $result = true;

        /**
         * Impl
         */

        return $result;
    }

    /**
     * Maps key to storage.
     *
     * @param $key
     * @return string
     */
    protected function key2storage($key)
    {
        $result = sha1($key);
        return substr($result, 0, $this->levels);
    }
}