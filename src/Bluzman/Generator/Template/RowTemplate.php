<?php
/**
 * Created by JetBrains PhpStorm.
 * User: bashmach
 * Date: 4/16/13
 * Time: 3:34 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Bluzman\Generator\Template;

/**
 * RowTemplate
 *
 * @category Generator
 * @package  Bluzman
 *
 * @author   Pavel Machekhin
 * @created  04/16/13 3:35 PM
 */

class RowTemplate extends AbstractTemplate
{
    /**
     * @return string
     */
    public function getTemplate()
    {
        $name = $this->options['name'];
        $author = get_current_user();
        $date = date('Y-m-d H:i:s');
        $properties = '';

        // set property type
        foreach ($this->options['columns'] as $column) {
            // all properties will be `string` except `bigint`, `int`, etc. columns
            $columnType = preg_match('/^int/', $column['type']) ? 'integer' : 'string';

            $properties .= " * @property " . $columnType . " $" . $column['name'] . "\r\n";
        }

        return <<<EOF
<?php

/**
 * @namespace
 */
namespace Application\\$name;

/**
 *
 *
 * @category Application
 * @package  $name
 *
$properties *
 * @author   $author
 * @created  $date
 */
class Row extends \Bluz\Db\Row
{
    /**
     * __insert
     *
     * @return void
     */
    public function beforeInsert()
    {

    }

    /**
     * __update
     *
     * @return void
     */
    public function beforeUpdate()
    {

    }
}
EOF;
    }
}