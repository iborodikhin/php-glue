<?php
namespace Glue\Test;

use Glue\Storage;
use Faker\Factory as FakerFactory;
use Faker\Provider;

class StorageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Glue\Storage
     */
    protected $storage;

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

        $this->storage = new \Glue\Storage(1, sys_get_temp_dir());

        $this->faker = FakerFactory::create();
        $this->faker->addProvider(new Provider\Internet($this->faker));
        $this->faker->addProvider(new Provider\DateTime($this->faker));

        $data           = implode(' ', $this->faker->sentences());
        $this->testData = array(
            'key'  => $this->faker->uuid,
            'data' => $data,
        );
        $this->storage->save($this->testData['key'], $this->testData['data']);
    }

    /**
     * @covers \Glue\Storage::save
     */
    public function testSave()
    {
        $data   = implode(' ', $this->faker->sentences());
        $result = $this->storage->save($this->faker->uuid, $data);

        $this->assertNotEquals(false, $result);
        $this->assertInternalType('int', $result);
        $this->assertEquals(strlen($data), $result);
    }

    /**
     * @covers \Glue\Storage::read
     */
    public function testRead()
    {
        $data = $this->storage->read($this->testData['key']);

        $this->assertNotEquals(false, $data);
        $this->assertEquals($this->testData['data'], $data['data']);
        $this->assertFalse($this->storage->read($this->faker->uuid));
    }

    /**
     * @covers \Glue\Storage::exists
     */
    public function testExists()
    {
        $this->assertTrue($this->storage->exists($this->testData['key']));
        $this->assertFalse($this->storage->exists($this->faker->uuid));
    }

    /**
     * @covers \Glue\Storage::delete
     */
    public function testDelete()
    {
        $this->assertTrue($this->storage->delete($this->testData['key']));
        $this->assertFalse($this->storage->delete($this->faker->uuid));
    }
}