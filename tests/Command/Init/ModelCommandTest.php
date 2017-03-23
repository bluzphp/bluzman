<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/bluzman
 */

namespace Bluzman\Tests\Command\Init;

use Bluzman\Command\Init;

use Faker;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @author Alexandr Kvasenko
 * @created 2015-03-30 13:33
 */
class ModelCommandTest extends AbstractCommandTest
{
    /**
     * @var string
     */
    protected $name = 'Users';

    /**
     * @var string
     */
    protected $table = 'users';

    /**
     * @var string
     */
    protected $modelPath;

    protected $dataForTemplate = ['author' => 'test', 'date' => '00-00-00 00:00:00'];

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

        $this->modelPath = $this->workingPath
            . DS . 'application'
            . DS . 'models'
            . DS . $this->name;
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
            ->shouldDeferMissing()
            ->shouldAllowMockingProtectedMethods();
        $templateRow->shouldReceive('getDefaultTemplateData')
            ->atLeast(1)
            ->andReturn($this->dataForTemplate)
            ->getMock();

        $templateTable = $container->mock('\Bluzman\Generator\Template\TableTemplate')
            ->shouldDeferMissing()
            ->shouldAllowMockingProtectedMethods();
        $templateTable->shouldReceive('getDefaultTemplateData')
            ->atLeast(1)
            ->andReturn($this->dataForTemplate)
            ->getMock();

        $command = $container->mock('\Bluzman\Command\Init\ModelCommand[getPrimaryKey, getColumns, getObjTemplate]')
            ->shouldDeferMissing()
            ->shouldAllowMockingProtectedMethods();
        $command->shouldReceive('getPrimaryKey')
            ->atLeast(1)
            ->andReturn($primaryKey)
            ->getMock();
        $command->shouldReceive('getColumns')
            ->atLeast(1)
            ->andReturn($columns)
            ->getMock();
        $command->shouldReceive('getObjTemplate')
            ->withArgs(['RowTemplate'])
            ->andReturn($templateRow)
            ->getMock();
        $command->shouldReceive('getObjTemplate')
            ->withArgs(['TableTemplate'])
            ->andReturn($templateTable)
            ->getMock();

        $this->getApplication()->addCommands([$command]);

        $commandTester = new CommandTester($command);

        $commandTester->execute(
            [
                'command' => $command->getName(),
                '--name' => $this->name,
                '--table' => $this->table
            ]
        );

        // check that all went well
        self::assertTrue($command->verify());

        $display = $commandTester->getDisplay();

        // check all messages were displayed
        self::assertRegExp('/Running "init:model" command/', $display);
        self::assertRegExp('/has been successfully created/', $display);

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
     *
     * @expectedException \Bluzman\Input\InputException
     */
    public function testValidateOptionException()
    {
        $container = new \Mockery\Container;
        $command = $container->mock('\Bluzman\Command\Init\ModelCommand[getPrimaryKey, getColumns]')
            ->shouldDeferMissing()
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
            ['command' => $command->getName(), '--name' => 'tes t', '--table' => $this->table],
            ['interactive' => false]
        );
        self::assertEquals($this->getExpectedException(), 'InputException');
    }
}
