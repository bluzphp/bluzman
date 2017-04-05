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
        self::assertInstanceOf('\\Bluzman\\Application\\Application', $this->application);
    }

    /**
     * Check if all commands has been registered.
     */
    public function testCommandsAreAvailable()
    {
        self::assertTrue($this->application->get('i-need-magic') instanceof Command);
        self::assertTrue($this->application->get('db:create') instanceof Command);
        self::assertTrue($this->application->get('db:migrate') instanceof Command);
        self::assertTrue($this->application->get('db:rollback') instanceof Command);
        self::assertTrue($this->application->get('db:seed:create') instanceof Command);
        self::assertTrue($this->application->get('db:seed:run') instanceof Command);
        self::assertTrue($this->application->get('db:seed:run') instanceof Command);
        self::assertTrue($this->application->get('generate:controller') instanceof Command);
        self::assertTrue($this->application->get('generate:crud') instanceof Command);
        self::assertTrue($this->application->get('generate:grid') instanceof Command);
        self::assertTrue($this->application->get('generate:model') instanceof Command);
        self::assertTrue($this->application->get('generate:module') instanceof Command);
        self::assertTrue($this->application->get('generate:rest') instanceof Command);
        self::assertTrue($this->application->get('module:install') instanceof Command);
        self::assertTrue($this->application->get('module:list') instanceof Command);
        self::assertTrue($this->application->get('module:remove') instanceof Command);
        self::assertTrue($this->application->get('server:start') instanceof Command);
        self::assertTrue($this->application->get('server:status') instanceof Command);
        self::assertTrue($this->application->get('server:stop') instanceof Command);
    }
}
