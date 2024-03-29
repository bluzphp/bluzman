<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/bluzman
 */

namespace Bluzman\Tests\Command\Generate;

use Symfony\Component\Console\Tester\CommandTester;

/**
 * @author Alexandr Kvasenko
 * @created 2015-03-30 13:33
 */
class ModelCommandTest extends AbstractCommandTest
{
    /**
     * @var string
     */
    protected $model = 'Users';

    /**
     * @var string
     */
    protected $table = 'users';

    /**
     * @var string
     */
    protected $modelPath;

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
    }

    /**
     * Testing correct create models
     *
     * @dataProvider dataProviderForCorrectWorkflow
     */
    public function testCorrectWorkflow($columns, $primaryKey, $rowTemplatePath, $tableTemplatePath)
    {
        $container = new \Mockery\Container;
        $templateRow = $container->mock('\Bluzman\Generator\Template\RowTemplate')
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
        $templateRow->shouldReceive('getDefaultTemplateData')
            ->atLeast(1)
            ->andReturn($this->dataForTemplate)
            ->getMock();

        $templateTable = $container->mock('\Bluzman\Generator\Template\TableTemplate')
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
        $templateTable->shouldReceive('getDefaultTemplateData')
            ->atLeast(1)
            ->andReturn($this->dataForTemplate)
            ->getMock();

        $command = $container->mock('\Bluzman\Command\Generate\ModelCommand[getPrimaryKey, getColumns, getTemplate]')
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
        $command->shouldReceive('getPrimaryKey')
            ->atLeast(1)
            ->andReturn($primaryKey)
            ->getMock();
        $command->shouldReceive('getColumns')
            ->atLeast(1)
            ->andReturn($columns)
            ->getMock();
        $command->shouldReceive('getTemplate')
            ->withArgs(['RowTemplate'])
            ->andReturn($templateRow)
            ->getMock();
        $command->shouldReceive('getTemplate')
            ->withArgs(['TableTemplate'])
            ->andReturn($templateTable)
            ->getMock();

        $this->getApplication()->addCommands([$command]);

        $commandTester = new CommandTester($command);

        $commandTester->execute(
            [
                'command' => $command->getName(),
                'model' => $this->model,
                'table' => $this->table
            ]
        );

        // check that all went well
        $command->verify($commandTester->getInput(), $commandTester->getOutput());

        $display = $commandTester->getDisplay();

        // check all messages were displayed
        self::assertMatchesRegularExpression('/Running generate:model command/', $display);
        self::assertMatchesRegularExpression('/has been successfully created/', $display);

        $table = $this->modelPath . DS . 'Table.php';
        $row = $this->modelPath . DS . 'Row.php';

        self::assertFileExists($table);
        self::assertEquals(md5_file($table), md5_file($tableTemplatePath));

        self::assertFileExists($row);
        self::assertEquals(md5_file($row), md5_file($rowTemplatePath));
    }

    public function dataProviderForCorrectWorkflow()
    {
        return [
            [
                'columns' => [
                    ['name' => 'id', 'type' => 'int'],
                    ['name' => 'name', 'type' => 'string'],
                    ['name' => 'desc', 'type' => 'string'],
                ],
                'primaryKey' => ['id'],
                'result-row-template-path' => __DIR__ . DS
                    . '..' . DS . '..' . DS
                    . 'Generator' . DS
                    . 'samples' . DS
                    . 'row.html',
                'result-table-template-path' => __DIR__ . DS
                    . '..' . DS . '..' . DS
                    . 'Generator' . DS
                    . 'samples' . DS
                    . 'table.html'

            ]
        ];
    }

    /**
     * Testing exception create models
     */
    public function testValidateOptionException()
    {
        $container = new \Mockery\Container;
        $command = $container->mock('\Bluzman\Command\Generate\ModelCommand[getPrimaryKey, getColumns]')
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
        $command->shouldReceive('getPrimaryKey')
            ->atLeast(1)
            ->andReturn(null)
            ->getMock();
        $command->shouldReceive('getColumns')
            ->atLeast(1)
            ->andReturn(null)
            ->getMock();

        $this->getApplication()->addCommands([$command]);

        $commandTester = new CommandTester($command);

        $commandTester->execute(
            [
                'command' => $command->getName(),
                'model' => '%%%%',
                'table' => '%%%%'
            ]
        );

        $display = $commandTester->getDisplay();

        self::assertMatchesRegularExpression('/ERROR/', $display);
    }
}
