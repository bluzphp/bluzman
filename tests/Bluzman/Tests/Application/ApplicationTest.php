<?php
/**
 * @author bashmach
 * @created 2013-12-07 01:21
 */

namespace Bluzman\Tests\Application;

use \Symfony\Component\Console\Command\Command;

class ApplicationTest extends TestCase
{
    /**
     * @var \Bluzman\Application\Application
     */
    protected $application;

    public function setUp()
    {
        $this->application = $this->getApplicationFixture();
    }

    /**
     * Verify fixture
     */
    public function testIsFixtureCorrect()
    {
        $this->assertInstanceOf('\Bluzman\Application\Application', $this->application);
    }

    /**
     * @depends testIsFixtureCorrect
     */
    public function testConfigProperty()
    {
        $config = $this->application->getConfig();

        $this->assertInstanceOf('\Bluzman\Application\Config', $config);
    }

    /**
     * Check if all commands has been registered.
     */
    public function testCommandsAreAvailable()
    {
        $this->assertTrue($this->application->get('server') instanceof Command);
        $this->assertTrue($this->application->get('test') instanceof Command);
        $this->assertTrue($this->application->get('init:all') instanceof Command);
    }
}