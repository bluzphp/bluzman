<?php

namespace Bluzman\Tests\Generator;
use Bluzman\Generator\Template\AbstractTemplate;

/**
 * DummyTemplate
 *
 * @category Generator
 * @package  Bluzman
 *
 * @author   Pavel Machekhin
 * @created  10/8/14 1:40 PM
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