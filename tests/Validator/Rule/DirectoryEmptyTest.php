<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/bluzman
 */

namespace Bluzman\Tests\Validator\Rule;

use Bluzman\Validator\Rule\DirectoryEmpty;

/**
 * @author Pavel Machekhin
 * @created 2014-01-04 00:52
 */
class DirectoryEmptyTest extends \PHPUnit_Framework_TestCase
{
    protected $emptyDir;

    protected $notEmptyDir;

    public function setUp()
    {
        $this->emptyDir = PATH_ROOT . DS . 'tests' . DS . 'Resources' . DS . 'fixtures' . DS . 'blank';
        $this->notEmptyDir = __DIR__;
    }

    public function tearDown()
    {
    }

    public function testEmptyDirectory()
    {
        $v = new DirectoryEmpty();

        self::assertTrue($v->validate($this->emptyDir));
    }

    /**
     * @expectedException \Bluz\Validator\Exception\ValidatorException
     */
    public function testNotEmptyDirectory()
    {
        $v = new DirectoryEmpty();
        $v->assert($this->notEmptyDir);
    }
}
