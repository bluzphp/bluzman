<?php
/**
 * @author bashmach
 * @created 2013-12-15 01:34
 */

namespace Bluzman\Tests\Application;


class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @return mixed
     */
    protected function getApplicationFixture()
    {
        $loader = new \Nelmio\Alice\Loader\Yaml();
        $fixture = $loader->load(__DIR__.'/fixtures/application.yml');
        return current($fixture);
    }
}