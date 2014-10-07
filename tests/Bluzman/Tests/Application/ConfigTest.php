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
        $this->container = new \Mockery\Container;

        if (!is_dir($this->getBluzmanTmpPath())) {
            mkdir($this->getBluzmanTmpPath());
        }

        $this->config = $this->container->mock(
            new \Bluzman\Application\Config($this->getApplicationFixture())
        );
    }

    public function tearDown()
    {
        if (is_file($this->getBluzmanTmpPath() . DS . 'config.json')) {
            unlink($this->getBluzmanTmpPath() . DS . 'config.json');
        }

        if (is_dir($this->getBluzmanTmpPath())) {
            rmdir($this->getBluzmanTmpPath());
        }

        m::close();
    }

    protected function getBluzmanTmpPath()
    {
        return PATH_TMP . DS . '.bluzman';
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
            ->andReturn(__DIR__ . DS . 'fixtures' . DS . 'app')
            ->shouldReceive('getBluzmanPath')
            ->andReturn(__DIR__ . DS . 'fixtures' . DS . 'app' . DS . '.bluzman')
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
            ->andReturn(PATH_TMP)
            ->shouldReceive('getBluzmanPath')
            ->andReturn($this->getBluzmanTmpPath())
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