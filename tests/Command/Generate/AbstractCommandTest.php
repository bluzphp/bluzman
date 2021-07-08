<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/bluzman
 */

namespace Bluzman\Tests\Command\Generate;

use Bluzman\Tests\BluzmanTestCase;
use Mockery\Container;

/**
 * @author Pavel Machekhin
 * @created 2014-07-10 15:24
 */
abstract class AbstractCommandTest extends BluzmanTestCase
{
    /**
     * {@inheritDoc}
     *
     * @throws \Mockery\Exception\RuntimeException
     */
    public function setUp(): void
    {
        parent::setUp();

        $container = new Container;

        $app = $container->mock('\Bluzman\Application\Application[getWorkingPath]')
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $app->shouldReceive('getWorkingPath')
            ->atLeast(1)
            ->andReturn($this->workingPath)
            ->getMock();

        $this->setApplication($app);
    }
}
