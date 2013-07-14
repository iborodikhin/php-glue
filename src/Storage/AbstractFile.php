<?php
/**
 * User: majesty
 * Date: 14.07.13
 */

namespace Glue\Storage;


abstract class AbstractFile
{
    /**
     * File path
     *
     * @var string
     */
    protected $path   = '';

    /**
     * File handle
     *
     * @var null
     */
    protected $handle = null;

    /**
     * File extension
     *
     * @var string
     */
    protected $extension = '';

    /**
     * Public constructor
     *
     * @param $path
     */
    public function __construct($path)
    {
        $this->path = $path . '.' . $this->extension;
    }

    /**
     * Public destructor
     */
    public function __destruct()
    {
        if (is_resource($this->handle)) {
            fclose($this->handle);
        }
    }

    /**
     * Opens storage file
     *
     * @return mixed
     */
    protected function open()
    {
        if (!is_resource($this->handle)) {
            $this->handle = fopen($this->path, 'r+');
        }

        return $this->handle;
    }

    /**
     * Returns file size
     *
     * @return int
     */
    protected function size()
    {
        return filesize($this->path);
    }
}