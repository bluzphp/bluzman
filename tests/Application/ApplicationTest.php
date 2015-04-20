<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/bluzman
 */


namespace Bluzman\Tests\Application;

use Bluzman\Tests\TestCase;
use \Symfony\Component\Console\Command\Command;

/**
 * @author Pavel Machekhin
 * @created 2013-12-07 01:21
 */

class ApplicationTest extends TestCase
{
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
        $this->assertTrue($this->application->get('server:start') instanceof Command);
        $this->assertTrue($this->application->get('test') instanceof Command);
        $this->assertTrue($this->application->get('init:all') instanceof Command);
    }
}
