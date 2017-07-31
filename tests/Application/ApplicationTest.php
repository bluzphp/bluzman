<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/bluzman
 */

namespace Bluzman\Tests\Application;

use Bluzman\Application\Application;
use Bluzman\Tests\BluzmanTestCase;
use Symfony\Component\Console\Command\Command;

/**
 * @author Pavel Machekhin
 * @created 2013-12-07 01:21
 */
class ApplicationTest extends BluzmanTestCase
{
    /**
     * Verify fixture
     */
    public function testIsFixtureCorrect()
    {
        self::assertInstanceOf(Application::class, $this->application);
    }

    /**
     * Check if all commands has been registered.
     */
    public function testCommandsAreAvailable()
    {
        self::assertInstanceOf(Command::class, $this->application->get('i-need-magic'));
        self::assertInstanceOf(Command::class, $this->application->get('run'));
        self::assertInstanceOf(Command::class, $this->application->get('test'));
        self::assertInstanceOf(Command::class, $this->application->get('db:create'));
        self::assertInstanceOf(Command::class, $this->application->get('db:migrate'));
        self::assertInstanceOf(Command::class, $this->application->get('db:rollback'));
        self::assertInstanceOf(Command::class, $this->application->get('db:seed:create'));
        self::assertInstanceOf(Command::class, $this->application->get('db:seed:run'));
        self::assertInstanceOf(Command::class, $this->application->get('db:seed:run'));
        self::assertInstanceOf(Command::class, $this->application->get('generate:controller'));
        self::assertInstanceOf(Command::class, $this->application->get('generate:crud'));
        self::assertInstanceOf(Command::class, $this->application->get('generate:grid'));
        self::assertInstanceOf(Command::class, $this->application->get('generate:model'));
        self::assertInstanceOf(Command::class, $this->application->get('generate:module'));
        self::assertInstanceOf(Command::class, $this->application->get('generate:rest'));
        self::assertInstanceOf(Command::class, $this->application->get('module:install'));
        self::assertInstanceOf(Command::class, $this->application->get('module:list'));
        self::assertInstanceOf(Command::class, $this->application->get('module:remove'));
        self::assertInstanceOf(Command::class, $this->application->get('server:start'));
        self::assertInstanceOf(Command::class, $this->application->get('server:status'));
        self::assertInstanceOf(Command::class, $this->application->get('server:stop'));
    }
}
