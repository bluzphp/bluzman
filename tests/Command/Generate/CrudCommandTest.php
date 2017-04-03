<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/bluzman
 */

namespace Bluzman\Tests\Command\Generate;

use Bluzman\Command\Generate\CrudCommand;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @author Anton Shevchuk
 */
class CrudCommandTest extends AbstractCommandTest
{
    /**
     * @var string
     */
    protected $model = 'Users';

    /**
     * @var string
     */
    protected $modelPath;

    protected $dataForTemplate = ['author' => 'test', 'date' => '00-00-00 00:00:00'];

    public function setUp()
    {
        parent::setUp();

        $this->modelPath = $this->workingPath
            . DS . 'application'
            . DS . 'models'
            . DS . $this->model;


        $this->getFs()->mkdir(
            $this->modelPath
        );
    }

    /**
     * Testing correct create CRUD
     */
    public function testCorrectWorkflow()
    {
        $container = new \Mockery\Container;
        $template = $container->mock('\Bluzman\Generator\Template\CrudTemplate[getDefaultTemplateData]')
            ->shouldDeferMissing()
            ->shouldAllowMockingProtectedMethods();
        $template->shouldReceive('getDefaultTemplateData')
            ->atLeast(1)
            ->andReturn($this->dataForTemplate)
            ->getMock();

        $command = $container->mock('\Bluzman\Command\Generate\CrudCommand[getTemplate]')
            ->shouldDeferMissing()
            ->shouldAllowMockingProtectedMethods();
        $command->shouldReceive('getTemplate')
            ->withArgs(['CrudTemplate'])
            ->andReturn($template)
            ->getMock();

        $this->getApplication()->addCommands([$command]);

        $commandTester = new CommandTester($command);

        $commandTester->execute(
            [
                'command' => $command->getName(),
                'model' => $this->model
            ]
        );

        // check that all went well
        $command->verify($commandTester->getInput(), $commandTester->getOutput());

        $display = $commandTester->getDisplay();

        // check all messages were displayed
        self::assertRegExp('/Running generate:crud command/', $display);
        self::assertRegExp('/has been successfully created/', $display);

        $file = $this->modelPath . DS . 'Crud.php';

        self::assertFileExists($file);
        self::assertEquals(
            md5_file($file),
            md5_file(__DIR__ . DS . '..' . DS . '..' . DS. 'Generator' . DS . 'samples' . DS . 'crud.html')
        );
    }

    /**
     * Testing exception create models
     */
    public function testValidateOptionException()
    {
        $command = new CrudCommand();

        $this->getApplication()->addCommands([$command]);

        $commandTester = new CommandTester($command);

        $commandTester->execute(
            [
                'command' => $command->getName(),
                'model' => '%%%%'
            ]
        );

        $display = $commandTester->getDisplay();

        self::assertRegExp('/ERROR/', $display);
    }
}
