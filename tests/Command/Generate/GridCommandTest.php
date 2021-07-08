<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/bluzman
 */

namespace Bluzman\Tests\Command\Generate;

use Bluzman\Command\Generate\GridCommand;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @author Anton Shevchuk
 */
class GridCommandTest extends AbstractCommandTest
{
    /**
     * @var string
     */
    protected $model = 'Users';

    /**
     * @var string
     */
    protected $modelPath;

    protected $dataForTemplate = [
        'author' => 'test',
        'date' => '00-00-00 00:00:00',
        'columns' => [
            'id' => ['type' => 'int'],
            'name' => ['type' => 'varchar'],
        ]
    ];

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
    }

    /**
     * Testing correct create CRUD
     */
    public function testCorrectWorkflow()
    {
        $container = new \Mockery\Container;
        $template = $container->mock('\Bluzman\Generator\Template\GridTemplate[getDefaultTemplateData]')
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
        $template->shouldReceive('getDefaultTemplateData')
            ->atLeast(1)
            ->andReturn($this->dataForTemplate)
            ->getMock();

        $command = $container->mock('\Bluzman\Command\Generate\GridCommand[getTemplate]')
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
        $command->shouldReceive('getTemplate')
            ->withArgs(['GridTemplate'])
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
        self::assertMatchesRegularExpression('/Running generate:grid command/', $display);
        self::assertMatchesRegularExpression('/has been successfully created/', $display);

        $file = $this->modelPath . DS . 'Grid.php';

        self::assertFileExists($file);
        self::assertEquals(
            md5_file($file),
            md5_file(__DIR__ . DS . '..' . DS . '..' . DS. 'Generator' . DS . 'samples' . DS . 'grid.html')
        );
    }

    /**
     * Testing exception create models
     */
    public function testValidateOptionException()
    {
        $command = new GridCommand();

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
