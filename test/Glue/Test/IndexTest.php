<?php
namespace Glue\Test;

use Glue\Storage\Index;
use Faker\Factory as FakerFactory;
use Faker\Provider;

class IndexTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $filename;

    /**
     * @var \Glue\Storage\Index
     */
    protected $index;

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

        $this->index = new Index($this->filename);

        $this->faker = FakerFactory::create();
        $this->faker->addProvider(new Provider\Internet($this->faker));
        $this->faker->addProvider(new Provider\DateTime($this->faker));

        $data           = implode(' ', $this->faker->sentences());
        $this->testData = array(
            'key'    => $this->faker->uuid,
            'data'   => $data,
            'length' => strlen($data),
        );

        $this->index->save($this->testData['key'], 0, $this->testData['length']);
    }

    /**
     * @covers \Glue\Storage\Index::read
     */
    public function testRead()
    {
        $result = $this->index->read($this->testData['key']);

        $this->assertNotEquals(false, $result);
        $this->assertInternalType('array', $result);
        $this->assertFalse($this->index->read($this->faker->uuid));
    }

    /**
     * @covers \Glue\Storage\Index::save
     */
    public function testSave()
    {
        $data   = implode(' ', $this->faker->sentences());
        $result = $this->index->save($this->faker->uuid, $this->testData['length'], strlen($data));

        $this->assertNotEquals(false, $result);
        $this->assertInternalType('int', $result);
    }

    /**
     * @covers \Glue\Storage\Index::delete
     */
    public function testDelete()
    {
        $result = $this->index->delete($this->testData['key']);

        $this->assertNotEquals(false, $result);
        $this->assertInternalType('array', $result);
    }
}