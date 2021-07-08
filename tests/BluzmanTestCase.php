<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/bluzman
 */

namespace Bluzman\Tests;

use Mockery;
use Nelmio\Alice\Loader\NativeLoader;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @author Pavel Machekhin
 * @created 2013-12-15 01:34
 */
class BluzmanTestCase extends \PHPUnit\Framework\TestCase
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
     * @return Filesystem
     */
    public function getFs(): Filesystem
    {
        return $this->fs;
    }

    /**
     * @param Filesystem $fs
     */
    public function setFs(Filesystem $fs)
    {
        $this->fs = $fs;
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

    /**
     * Setup FileSystem and Application fixture
     */
    public function setUp(): void
    {
        $this->setFs(new Filesystem());

        // generate unique working path for future use in commands
        $this->setWorkingPath(PATH_TMP . DS . uniqid('bluzman_'));

        $loader = new NativeLoader();
        $fixture = $loader->loadfile(__DIR__ . DS . 'Resources' . DS . 'fixtures' . DS . 'application.yml');
        $this->setApplication($fixture->getObjects()['app']);
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

    public function tearDown(): void
    {
        Mockery::close();

        if (is_dir($this->getWorkingPath())) {
            $this->getFs()->remove($this->getWorkingPath());
        }
    }
}
