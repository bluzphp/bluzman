<?php

namespace Bluzman\Generator\Template;

/**
 * ViewTemplate
 *
 * @category Generator
 * @package  Bluzman
 *
 * @author   Pavel Machekhin
 * @created  4/06/13 12:48 AM
 */

class ViewTemplate extends AbstractTemplate
{
    /**
     * @return string
     */
    public function getTemplate()
    {
        $name = $this->options['name'];

        return <<<EOF
<p>This is the $name view</p>
EOF;

    }
}