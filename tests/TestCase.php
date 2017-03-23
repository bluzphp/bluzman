<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/bluzman
 */

namespace Bluzman\Tests;

use Faker;
use Mockery;
use Nelmio\Alice\Fixtures\Loader;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @author Pavel Machekhin
 * @created 2013-12-15 01:34
 */
class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Bluzman\Application\Application
     */
    protected $application;

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

    /**
     * @return Filesystem
     */
    public function getFs()
    {
        return $this->fs;
    }

    /**
     * @param Filesystem $fs
     */
    public function setFs($fs)
    {
        $this->fs = $fs;
    }

    /**
     * @return Faker\Generator
     */
    public function getFaker()
    {
        if (!$this->faker) {
            $this->faker = Faker\Factory::create();
        }

        return $this->faker;
    }

    /**
     * @param Faker\Generator $faker
     */
    public function setFaker($faker)
    {
        $this->faker = $faker;
    }

    /**
     * @return \Bluzman\Application\Application
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * @param \Bluzman\Application\Application $application
     */
    public function setApplication($application)
    {
        $this->application = $application;
    }

    public function setUp()
    {
        $this->setFs(new Filesystem());

        // generate unique working path for future use in commands
        $this->setWorkingPath(PATH_TMP . DS . $this->getFaker()->uuid);

        $loader = new Loader();
        $fixture = $loader->load(__DIR__ . DS . 'Resources' . DS . 'fixtures' . DS . 'application.yml');

        $this->setApplication(current($fixture));
    }

    /**
     * @return string
     */
    public function getWorkingPath()
    {
        return $this->workingPath;
    }

    /**
     * @param string $workingPath
     */
    public function setWorkingPath($workingPath)
    {
        $this->workingPath = $workingPath;

        if (!$this->fs->exists($this->workingPath)) {
            $this->fs->mkdir($this->workingPath);
        }
    }

    public function tearDown()
    {
        Mockery::close();

        if (is_dir($this->getWorkingPath())) {
            $this->getFs()->remove($this->getWorkingPath());
        }
    }
}
