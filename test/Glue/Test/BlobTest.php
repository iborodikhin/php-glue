<?php
namespace Glue\Test;

use Glue\Storage\Blob;
use Faker\Factory as FakerFactory;
use Faker\Provider;

class BlobTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $filename;

    /**
     * @var \Glue\Storage\Blob
     */
    protected $blob;

    /**
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * @var array
     */
    protected $testData = array();

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->filename = sys_get_temp_dir() . '/' . time();
        $this->blob     = new Blob($this->filename);

        $this->faker = FakerFactory::create();
        $this->faker->addProvider(new Provider\Internet($this->faker));
        $this->faker->addProvider(new Provider\DateTime($this->faker));

        $data   = implode(' ', $this->faker->sentences());
        $length = strlen($data);
        $result = $this->blob->save($data);

        $this->testData = array(
            'offset' => $result[0],
            'length' => $length,
            'data'   => $data,
        );
    }

    /**
     * @covers \Glue\Storage\Blob::save
     */
    public function testSave()
    {
        $data   = $this->faker->sentence();
        $result = $this->blob->save($data);

        $this->assertNotEquals(false, $result);
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('0', $result);
        $this->assertArrayHasKey('1', $result);
        $this->assertEquals(strlen($data), $result[1]);
    }

    /**
     * @covers \Glue\Storage\Blob::read
     */
    public function testRead()
    {
        $result = $this->blob->read($this->testData['offset'], $this->testData['length']);

        $this->assertNotEquals(false, $result);
        $this->assertEquals($this->testData['data'], $result);
        $this->assertFalse($this->blob->read(PHP_INT_MAX - 10, 10));
    }

    /**
     * @covers \Glue\Storage\Blob::delete
     */
    public function testDelete()
    {
        $result = $this->blob->delete($this->testData['offset'], $this->testData['length']);

        $this->assertNotEquals(false, $result);
    }
}