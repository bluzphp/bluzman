<?php
/**
 * @author bashmach
 * @created 10/7/14 3:24 PM
 */

namespace Bluzman\Tests\Command\Init;

use Bluzman\Application\Application;
use Mockery as m;
use Faker;
use Symfony\Component\Filesystem\Filesystem;

abstract class AbstractCommandTest  extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var string
     */
    protected $workingPath;

    /**
     * @var Filesystem
     */
    protected $fs;

    /**
     * @var Faker\Generator
     */
    protected $faker;

    public function setUp()
    {
        $this->faker = Faker\Factory::create();

        // generate unique working path for future use in commands
        $this->workingPath = PATH_TMP . DS . $this->faker->uuid;

        $this->fs = new Filesystem();
        $this->fs->mkdir($this->workingPath);
    }

    public function tearDown()
    {
        m::close();

        if (is_dir($this->workingPath)) {
            $this->fs->remove($this->workingPath);
        }
    }
} 