<?php
namespace Glue\Test;

use Glue\Glue;
use Faker\Factory as FakerFactory;
use Faker\Provider;

class GlueTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Glue\Glue
     */
    protected $glue;

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

        $this->glue = new Glue(sys_get_temp_dir(), 1);

        $this->faker = FakerFactory::create();
        $this->faker->addProvider(new Provider\Internet($this->faker));
        $this->faker->addProvider(new Provider\DateTime($this->faker));

        $this->testData = array(
            'name' => $this->faker->uuid,
            'data' => implode(' ', $this->faker->sentences()),
        );

        $this->glue->save($this->testData['name'], $this->testData['data']);
    }

    /**
     * @covers \Glue\Glue::save
     */
    public function testSave()
    {
        $name   = $this->faker->uuid;
        $data   = implode(' ', $this->faker->sentences());
        $result = $this->glue->save($name, $data);

        $this->assertNotEquals(false, $result);
        $this->assertInternalType('int', $result);
        $this->assertEquals(strlen($data), $result);
    }

    /**
     * @covers \Glue\Glue::read
     */
    public function testRead()
    {
        $result = $this->glue->read($this->testData['name']);

        $this->assertNotEquals(false, $result);
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('meta', $result);
        $this->assertArrayHasKey('data', $result);
        $this->assertEquals($this->testData['data'], $result['data']);
    }

    /**
     * @covers \Glue\Glue::exists
     */
    public function testExists()
    {
        $this->assertFalse($this->glue->exists($this->faker->uuid));
        $this->assertTrue($this->glue->exists($this->testData['name']));
    }

    /**
     * @covers \Glue\Glue::delete
     */
    public function testDelete()
    {
        $this->assertFalse($this->glue->delete($this->faker->uuid));
        $this->assertTrue($this->glue->delete($this->testData['name']));
    }
}