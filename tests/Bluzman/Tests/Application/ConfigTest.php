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

    public function setUp()
    {
        $this->config = new Config($this->getApplicationFixture());

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
        $this->config->setApplication($this->getMockWithFixturesWorkingPath());
        $this->assertInstanceOf('\Bluzman\Application\Config', $this->config);
        $this->assertNotEquals($this->config->getApplication()->getWorkingPath(), __DIR__);
    }


    public function testMagicGetter()
    {
        $this->config->setApplication($this->getMockWithFixturesWorkingPath());
        $this->assertEquals($this->config->foo, 'bar');
    }

    /**
     * Test magic setter which is not implemented yet.
     *
     * @expectedException \Exception
     */
    public function testMagicSetter()
    {
        $this->config->setApplication($this->getMockWithTemporaryWorkingPath());
        $this->config->bar = 'foo';

        $this->assertEquals('foo', $this->config->bar);
    }

    public function testConfigCreate()
    {
        $this->config->setApplication($this->getMockWithTemporaryWorkingPath());
        $this->config->putOptions(['foo' => 'bar']);

        $this->assertFileExists(PATH_TMP . DS . '.bluzman' . DS . 'config.json');
    }

    /**
     * @depends testConfigCreate
     * @dataProvider foobarOptions
     */
    public function testConfigRead($data)
    {
        $this->config->setApplication($this->getMockWithTemporaryWorkingPath());
        $this->config->putOptions($data);

        $options = $this->config->getOptions();

        $this->assertEquals($data, $options);
    }

    /**
     *
     */
    public function testGetBluzConfig()
    {
        $this->config->setApplication($this->getMockWithFixturesWorkingPath());

        /**
         * @var \Bluz\Config\Config $bluzConfig
         */
        $bluzConfig = $this->config->getBluzConfig();

        $this->assertInstanceOf('\Bluz\Config\Config', $bluzConfig);
        $this->assertEquals('bar', $bluzConfig->getData('foo'));
        $this->assertEquals('foo', $bluzConfig->getData('bar'));
    }

    /**
     * @return \Bluz\Application\Application
     */
    protected function getMockWithFixturesWorkingPath()
    {
        $mock = m::mock('Bluz\Application\Application')
            ->shouldReceive('getWorkingPath')
            ->atLeast(1)
            ->andReturn(__DIR__ . DS . 'fixtures' . DS . 'app')
            ->getMock();

        return $mock;
    }

    /**
     * @return \Bluz\Application\Application
     */
    protected function getMockWithTemporaryWorkingPath()
    {
        $mock = m::mock('Bluz\Application\Application')
            ->shouldReceive('getWorkingPath')
            ->atLeast(1)
            ->andReturn(PATH_TMP)
            ->getMock();

        return $mock;
    }

    public function foobarOptions()
    {
        return [
            [['foo' => 'bar']]
        ];
    }
}