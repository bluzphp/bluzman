<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/bluzman
 */

namespace Bluzman\Tests\Validator\Rule;

use Bluzman\Validator\Rule\Directory;

class DirectoryTest extends \PHPUnit_Framework_TestCase
{
    protected $directories = [];

    public function setUp()
    {
        $this->directories = [
            sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'dataprovider-1',
            sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'dataprovider-2',
            sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'dataprovider-3',
            sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'dataprovider-4',
            sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'dataprovider-5',
        ];

//        var_dump($this->directories);

        foreach ($this->directories as $directory) {
            if (!is_dir($directory)) {
                mkdir($directory, 0766, true);
            }
        }
    }

    public function tearDown()
    {
        foreach ($this->directories as $directory) {
            if (is_dir($directory)) {
                rmdir($directory);
            }
        }
    }

    /**
     * @dataProvider providerForValidDirectory
     */
    public function testValidDirectoryShouldReturnTrue($input)
    {
        $rule = new Directory();
        self::assertTrue($rule->__invoke($input));
        self::assertTrue($rule->assert($input));
        self::assertTrue($rule->validate($input));
    }

    /**
     * @dataProvider providerForInvalidDirectory
     * @expectedException \Bluz\Validator\Exception\ValidatorException
     */
    public function testInvalidDirectoryShouldThrowException($input)
    {
        $rule = new Directory();
        self::assertFalse($rule->__invoke($input));
        self::assertFalse($rule->assert($input));
        self::assertFalse($rule->validate($input));
    }

    /**
     * @dataProvider providerForDirectoryObjects
     */
    public function testDirectoryWithObjects($object, $valid)
    {
        $rule = new Directory();
        self::assertEquals($valid, $rule->validate($object));
    }

    public function providerForDirectoryObjects()
    {
        return array(
            array(new \SplFileInfo(__DIR__), true),
            array(new \SplFileInfo(__FILE__), false),
            /**
             * PHP 5.4 does not allows to use SplFileObject with directories.
             * array(new \SplFileObject(__DIR__), true),
             */
            array(new \SplFileObject(__FILE__), false),
        );
    }

    public function providerForValidDirectory()
    {
        return $this->directories;
    }

    public function providerForInvalidDirectory()
    {
        return array_chunk(
            array(
                __FILE__,
                __DIR__ . '/../../../../../README.md',
                __DIR__ . '/../../../../../composer.json',
                new \stdClass(),
                array(__DIR__),
            ),
            1
        );
    }
}
