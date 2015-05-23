<?php
namespace Glue;

use Glue\Storage;

class Glue
{
    /**
     * Directory in which data stored
     *
     * @var string
     */
    protected $path;

    /**
     * How many BLOBs to create — 16^$levels
     *
     * @var integer
     */
    protected $levels = 2;

    /**
     * Storage objects local cache
     *
     * @var \Glue\Storage[]
     */
    protected $storages = array();

    /**
     * Public constructor.
     * Creates Glue instance.
     *
     * @param  string  $path
     * @param  integer $levels
     * @thrown \InvalidArgumentException
     */
    public function __construct($path, $levels = 2)
    {
        if (empty($path)) {
            throw new \InvalidArgumentException('"path" parameter is required.');
        }

        $this->path   = $path;
        $this->levels = $levels;
    }

    /**
     * Save data to storage.
     *
     * @param  string          $name
     * @param  string          $value
     * @return boolean|integer
     */
    public function save($name, $value)
    {
        $key     = $this->key2storage($name);
        $storage = $this->getStorage($key);
        $result  = $storage->save($name, $value);

        return $result;
    }

    /**
     * Read data from storage.
     *
     * @param  string $name
     * @return string
     */
    public function read($name)
    {
        $key     = $this->key2storage($name);
        $storage = $this->getStorage($key);
        $result  = $storage->read($name);

        return $result;
    }

    /**
     * Checks if item exists in storage.
     *
     * @param  string  $name
     * @return boolean
     */
    public function exists($name)
    {
        $key = $this->key2storage($name);
        $storage = $this->getStorage($key);

        return $storage->exists($name);
    }

    /**
     * Remove data from storage.
     *
     * @param  string  $name
     * @return boolean
     */
    public function delete($name)
    {
        $key     = $this->key2storage($name);
        $storage = $this->getStorage($key);
        $result  = $storage->delete($name);

        return $result;
    }

    /**
     * Compact storage.
     *
     * @return boolean
     */
    public function compact()
    {
        $result = true;

        $min = 0;
        $max = pow(16, $this->levels);
        for ($i = $min; $i < $max; $i ++) {
            $key = dechex($i);
            if (strlen($key) < $this->levels) {
                $key = str_pad($key, $this->levels, '0', STR_PAD_LEFT);
            }
            $result = $this->getStorage($key)->compact() && $result;
        }

        return $result;
    }

    /**
     * Maps key to storage.
     *
     * @param  string $key
     * @return string
     */
    protected function key2storage($key)
    {
        $result = sha1($key);

        return substr($result, 0, $this->levels);
    }

    /**
     * Returns Storage object with cache
     *
     * @param  string        $key
     * @return \Glue\Storage
     */
    protected function getStorage($key)
    {
        if (!isset($this->storages[$key]) || !is_a($this->storages[$key], Storage)) {
            $this->storages[$key] = new Storage($key, $this->path);
        }

        return $this->storages[$key];
    }
}
