<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/bluzman
 */

namespace Bluzman\Tests\Command\Init;

use Bluzman\Command\Init;
use Bluzman\Tests\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;

/**
 * @author Pavel Machekhin
 * @created 2014-01-05 00:10
 */
class AllCommandTest extends TestCase
{
    protected $projectName;

    public function setUp()
    {
        parent::setUp();

        $this->projectName = 'test';
    }

    /**
     * @expectedException \Bluzman\Input\InputException
     */
    public function testExecuteWithoutParams()
    {
        $command = new Init\AllCommand;

        $this->getApplication()->addCommands([$command]);

        $commandTester = new CommandTester($command);
        $commandTester->execute(
            ['command' => $command->getName()],
            ['interactive' => false]
        );
    }

    /**
     * @expectedException \Bluzman\Input\InputException
     */
    public function testNameOptionWithSpaces()
    {
        $command = new Init\AllCommand;

        $this->getApplication()->addCommands([$command]);

        $commandTester = new CommandTester($command);
        $commandTester->execute(
            ['command' => $command->getName(), '--name' => 'test me now'],
            ['interactive' => false]
        );
    }

    /**
     * @expectedException \Bluzman\Input\InputException
     */
    public function testNotWritablePathException()
    {
        $command = new Init\AllCommand;

        $this->getApplication()->addCommands([$command]);

        $commandTester = new CommandTester($command);
        $commandTester->execute(
            ['command' => $command->getName(), '--name' => $this->projectName, '--path' => '/root'],
            ['interactive' => false]
        );
    }

    /**
     * @expectedException \Bluzman\Input\InputException
     */
    public function testNotExistsPathException()
    {
        $command = new Init\AllCommand;

        $this->getApplication()->addCommands([$command]);

        $commandTester = new CommandTester($command);
        $commandTester->execute(
            ['command' => $command->getName(), '--name' => $this->projectName, '--path' => '/somepath'],
            ['interactive' => false]
        );
    }

    /**
     * @expectedException \Bluzman\Input\InputException
     */
    public function testNotEmptyPathException()
    {
        mkdir($this->workingPath . DS . $this->projectName);

        touch($this->workingPath . DS . $this->projectName . DS . 'stub');

        $command = new Init\AllCommand;

        $this->getApplication()->addCommands([$command]);

        $commandTester = new CommandTester($command);
        $commandTester->execute(
            ['command' => $command->getName(), '--name' => $this->projectName, '--path' => $this->workingPath],
            ['interactive' => false]
        );
    }

    /**
     *
     */
    public function testWithCorrectParams()
    {
        $container = new \Mockery\Container;

        $commandMock = $container->mock('\Bluzman\Command\Init\AllCommand[getCmdPattern]', ['init:all'])
            ->shouldDeferMissing()
            ->shouldAllowMockingProtectedMethods();

        $commandMock->shouldReceive('getCmdPattern')
            ->atLeast(1)
            ->andReturn('mkdir %s')
            ->getMock();

        $configMock = $container->mock('\Bluzman\Application\Config[putOptions]', [$this->getApplication()])
            ->shouldReceive('putOptions')
            ->atLeast(1)
            ->andReturn(true)
            ->getMock();

        self::assertInstanceOf('\Bluzman\Command\Init\AllCommand', $commandMock);
        self::assertInstanceOf('\Bluzman\Application\Config', $configMock);

        $this->getApplication()->addCommands([$commandMock]);
        $this->getApplication()->setConfig($configMock);

        $commandTester = new CommandTester($commandMock);
        $commandTester->execute(
            [
                'command' => $commandMock->getName(),
                '--name' => $this->projectName,
                '--path' => $this->workingPath
            ]
        );

        $display = $commandTester->getDisplay();

        self::assertRegExp('/Running "init:all" command/', $display);
        self::assertRegExp('/Cloning skeleton project/', $display);
        self::assertRegExp('/has been successfully initialized/', $display);

        self::assertFileExists($this->workingPath . DS . $this->projectName . DS . '.bluzman');
    }
}
