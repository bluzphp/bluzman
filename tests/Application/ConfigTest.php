<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/bluzman
 */

namespace Bluzman\Tests\Application;

use Bluzman\Application\Config;
use Bluzman\Tests\TestCase;
use \Mockery as m;

/**
 * @author Pavel Machekhin
 * @created 2013-12-07 01:56
 */
class ConfigTest extends TestCase
{
    /**
     * @var \Bluzman\Application\Config
     */
    protected $config;

    public function setUp()
    {
        parent::setUp();

        $this->container = new \Mockery\Container;

        if (!is_dir($this->getBluzmanWorkingPath())) {
            mkdir($this->getBluzmanWorkingPath());
        }

        $this->config = new \Bluzman\Application\Config($this->getApplication());
    }

    protected function getBluzmanWorkingPath()
    {
        return $this->getWorkingPath() . DS . '.bluzman';
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

        $this->assertFileExists($this->getWorkingPath() . DS . '.bluzman' . DS . 'config.json');
    }

    /**
     * @depends      testConfigCreate
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
        $bluzConfig = $this->config->getBluzConfig('dev');

        $this->assertInstanceOf('\Bluz\Config\Config', $bluzConfig);
        $this->assertEquals(['foo' => 'bar'], $bluzConfig->getData('foo'));
        $this->assertEquals(['bar' => 'foo'], $bluzConfig->getData('bar'));
    }

    /**
     * @return \Bluz\Application\Application
     */
    protected function getMockWithFixturesWorkingPath()
    {
        $mock = $this->container->mock('Bluz\Application\Application')
            ->shouldReceive('getWorkingPath')
            ->atLeast(1)
            ->andReturn(__DIR__ . DS . 'Resources' . DS . 'fixtures' . DS . 'app')
            ->shouldReceive('getBluzmanPath')
            ->andReturn(__DIR__ . DS . 'Resources' . DS . 'fixtures' . DS . 'app' . DS . '.bluzman')
            ->getMock();

        return $mock;
    }

    /**
     * @return \Bluz\Application\Application
     */
    protected function getMockWithTemporaryWorkingPath()
    {
        $mock = $this->container->mock('Bluz\Application\Application')
            ->shouldReceive('getWorkingPath')
            ->atLeast(1)
            ->andReturn($this->getWorkingPath())
            ->shouldReceive('getBluzmanPath')
            ->andReturn($this->getBluzmanWorkingPath())
            ->getMock();

        return $mock;
    }

    public function foobarOptions()
    {
        return [
            [
                [
                    'foo' => [
                        'foo' => 'bar'
                    ]
                ]
            ]
        ];
    }
}
