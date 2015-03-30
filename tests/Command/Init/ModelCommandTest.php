<?php
/**
 * Created by PhpStorm.
 * User: kvasenko
 * Date: 30.03.15
 * Time: 13:33
 */

namespace Bluzman\Tests\Command\Init;

use Bluzman\Command\Init;

use Faker;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;

class ModelCommandTest extends AbstractCommandTest
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $table;

    public function setUp()
    {
        parent::setUp();

        $container = new \Mockery\Container;

        $app = $container->mock('\Bluzman\Application\Application[getWorkingPath]')
            ->shouldDeferMissing()
            ->shouldAllowMockingProtectedMethods();

        $app->shouldReceive('getWorkingPath')
            ->atLeast(1)
            ->andReturn($this->workingPath)
            ->getMock();

        $this->setApplication($app);

        $this->table = 'users';
        $this->name = 'Users';
        $this->modelPath = $this->workingPath
            . DS . 'application'
            . DS . 'models'
            . DS . $this->name;
    }

    /**
     * Testing correct create models
     */
    public function testCorrectWorkflow()
    {
        $command = new Init\ModelCommand();

        $this->getApplication()->addCommands([$command]);

        $commandTester = new CommandTester($command);

        $commandTester->execute([
            'command' => $command->getName(),
            '--name' => $this->name,
            '--table' => $this->table
        ]);

        // check that all went well
        $this->assertTrue($command->verify());

        $display = $commandTester->getDisplay();

        // check all messages were displayed
        $this->assertRegExp('/Running "init:model" command/', $display);
        $this->assertRegExp('/has been successfully created/', $display);

        $this->assertFileExists(
            $this->modelPath
            . DS . 'Table' . '.php'
        );
    }
}
