<?php
namespace Glue;

use Glue\Storage\Index;
use Glue\Storage\Blob;

class Storage
{
    /**
     * Index instance
     *
     * @var \Glue\Storage\Index
     */
    protected $index = null;

    /**
     * Blob instance
     *
     * @var \Glue\Storage\Blob
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
     * @param string $key
     * @param string $path
     */
    public function __construct($key, $path)
    {
        $this->key   = $key;
        $this->path  = $path;

        $this->index = new Index($this->getKeyPath());
        $this->blob  = new Blob($this->getKeyPath());
    }

    /**
     * Saves item to storage
     *
     * @param  string          $name
     * @param  string          $value
     * @param  array           $meta
     * @return boolean|integer
     */
    public function save($name, $value, array $meta = array())
    {
        $key   = $this->getKey($name);
        $index = $this->blob->save($value);
        $meta['mime-type'] = $this->getMimeType($value);

        if ($index === false) {
            return false;
        }

        return $this->index->save($key, $index[0], $index[1], serialize($meta));
    }

    /**
     * Reads item from storage
     *
     * @param  string        $name
     * @return array|boolean
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
        $meta   = unserialize($index[2]);
        $data   = $this->blob->read($offset, $length);

        return array(
            'data' => $data,
            'meta' => $meta,
        );
    }

    /**
     * Deletes item from storage
     *
     * @param  string  $name
     * @return boolean
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
     * @return boolean
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
     * @param  string $name
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

    /**
     * Returns mime-type for data
     *
     * @param  string $data
     * @return string
     */
    protected function getMimeType($data)
    {
        $info = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_buffer($info, $data);
        finfo_close($info);

        return $mime;
    }
}
