<?php
/**
 * @author bashmach
 * @created 2013-12-07 01:56
 */

namespace Bluzman\Tests\Application;

use Bluzman\Application\Config;
use \Mockery as m;

class ConfigTest extends TestCase
{
    /**
     * @var \Bluzman\Application\Config
     */
    protected $config;

    protected $data = ['foo' => 'bar'];

    public function setUp()
    {
        $mock = m::mock('Bluz\Application\Application')
            ->shouldReceive('getWorkingPath')
            ->atLeast(1)
            ->andReturn(__DIR__ . DS . 'fixtures' . DS . 'app')
            ->getMock();

        $config = new Config($this->getApplicationFixture());
        $config->setApplication($mock);

        $this->config = $config;

        mkdir(PATH_TMP . DS . '.bluzman');
    }

    public function tearDown()
    {
        if (is_file(PATH_TMP . DS . '.bluzman' . DS . 'config.json')) {
            unlink(PATH_TMP . DS . '.bluzman' . DS . 'config.json');
        }

        if (is_dir(PATH_TMP . DS . '.bluzman')) {
            rmdir(PATH_TMP . DS . '.bluzman');
        }


        m::close();
    }

    /**
     * Verify fixture
     */
    public function testIsFixtureCorrect()
    {
        $this->assertInstanceOf('\Bluzman\Application\Config', $this->config);
        $this->assertNotEquals($this->config->getApplication()->getWorkingPath(), __DIR__);
    }


    public function testMagicGetter()
    {
        $this->assertEquals($this->config->foo, 'bar');
    }

    /**
     * Test magic setter which is not implemented yet.
     *
     * @expectedException \Exception
     */
    public function testMagicSetter()
    {
        $this->config->bar = 'foo';

        $this->assertEquals('foo', $this->config->bar);
    }

    public function testConfigCreate()
    {
        $mock = m::mock('Bluz\Application\Application')
            ->shouldReceive('getWorkingPath')
            ->atLeast(1)
            ->andReturn(PATH_TMP)
            ->getMock();

        $this->config->setApplication($mock);
        $this->config->putOptions(['foo' => 'bar']);

        $this->assertFileExists(PATH_TMP . DS . '.bluzman' . DS . 'config.json');
    }

    /**
     * @depends testConfigCreate
     * @dataProvider foobarOptions
     */
    public function testConfigRead($data)
    {
        $mock = m::mock('Bluz\Application\Application')
            ->shouldReceive('getWorkingPath')
            ->atLeast(1)
            ->andReturn(PATH_TMP)
            ->getMock();

        $this->config->setApplication($mock);
        $this->config->putOptions($data);

        $options = $this->config->getOptions();

        $this->assertEquals($data, $options);
    }

    /**
     *
     */
    public function testGetBluzConfig()
    {
        /**
         * @var \Bluz\Config\Config $bluzConfig
         */
        $bluzConfig = $this->config->getBluzConfig();

        $this->assertInstanceOf('\Bluz\Config\Config', $bluzConfig);
        $this->assertEquals('bar', $bluzConfig->getData('foo'));
        $this->assertEquals('foo', $bluzConfig->getData('bar'));
    }

    public function foobarOptions()
    {
        return [
            [['foo' => 'bar']]
        ];
    }
}