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
class RestCommandTest extends AbstractCommandTest
{
    /**
     * @var string
     */
    protected $model = 'Users';

    /**
     * @var string
     */
    protected $module = 'users';

    /**
     * @var string
     */
    protected $modelPath;

    /**
     * @var string
     */
    protected $modulePath;

    /**
     * @var array
     */
    protected $dataForTemplate = ['author' => 'test', 'date' => '00-00-00 00:00:00'];

    /**
     * {@inheritDoc}
     *
     * @throws \Mockery\Exception\RuntimeException
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->modelPath = $this->workingPath
            . DS . 'application'
            . DS . 'models'
            . DS . $this->model;

        $this->getFs()->mkdir(
            $this->modelPath
        );

        $this->getFs()->touch(
            $this->modelPath .DS. 'Crud.php'
        );

        $this->modulePath = $this->workingPath
            . DS . 'application'
            . DS . 'modules'
            . DS . $this->module;


        $this->getFs()->mkdir(
            $this->modulePath
        );

    }

    /**
     * Testing correct create CRUD
     */
    public function testCorrectWorkflow()
    {
        $container = new \Mockery\Container;
        $template = $container->mock('\Bluzman\Generator\Template\RestTemplate[getDefaultTemplateData]')
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
        $template->shouldReceive('getDefaultTemplateData')
            ->atLeast(1)
            ->andReturn($this->dataForTemplate)
            ->getMock();

        $command = $container->mock('\Bluzman\Command\Generate\RestCommand[getTemplate]')
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
        $command->shouldReceive('getTemplate')
            ->withArgs(['RestTemplate'])
            ->andReturn($template)
            ->getMock();

        $this->getApplication()->addCommands([$command]);

        $commandTester = new CommandTester($command);

        $commandTester->execute(
            [
                'command' => $command->getName(),
                'model' => $this->model,
                'module' => $this->module
            ]
        );

        // check that all went well
        $command->verify($commandTester->getInput(), $commandTester->getOutput());

        $display = $commandTester->getDisplay();

        // check all messages were displayed
        self::assertMatchesRegularExpression('/Running generate:rest command/', $display);
        self::assertMatchesRegularExpression('/has been successfully created/', $display);

        $file = $this->modulePath .DS. 'controllers' .DS. 'rest.php';

        self::assertFileExists($file);

        self::assertEquals(
            md5_file($file),
            md5_file(__DIR__ . DS . '..' . DS . '..' . DS. 'Generator' . DS . 'samples' . DS . 'rest.html')
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

        self::assertMatchesRegularExpression('/ERROR/', $display);
    }
}
