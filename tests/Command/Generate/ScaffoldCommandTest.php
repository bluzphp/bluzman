<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/bluzman
 */

namespace Bluzman\Tests\Command\Generate;

use Bluzman\Command\Generate\ScaffoldCommand;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @author Anton Shevchuk
 */
class ScaffoldCommandTest extends AbstractCommandTest
{
    /**
     * Testing exception create models
     */
    public function testValidateOptionException()
    {
        $command = new ScaffoldCommand();

        $this->getApplication()->addCommands([$command]);

        $commandTester = new CommandTester($command);

        $commandTester->execute(
            [
                'command' => $command->getName(),
                'table' => '%%%%',
                'model' => '%%%%',
                'module' => '%%%%'
            ]
        );

        $display = $commandTester->getDisplay();

        self::assertMatchesRegularExpression('/ERROR/', $display);
    }
}
