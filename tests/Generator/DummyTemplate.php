<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/bluzman
 */

namespace Bluzman\Tests\Generator;
use Bluzman\Generator\Template\AbstractTemplate;

/**
 * DummyTemplate
 *
 * @category Generator
 * @package  Bluzman
 *
 * @author   Pavel Machekhin
 * @created  2014-08-10 13:40
 */

class DummyTemplate extends AbstractTemplate
{
    protected $templatePath = 'views/dummy.html';

    /**
     * Rewrite the defaults from AbstractTemplate
     *
     * @return array
     */
    public function getDefaultTemplateData()
    {
        return [];
    }
}