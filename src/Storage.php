<?php
/**
 * User: majesty
 * Date: 14.07.13
 */

namespace Glue;


class Storage
{
    protected $index = null;

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

    public function save($name, $value)
    {

    }

    public function read($name)
    {

    }

    public function delete($name)
    {

    }
}