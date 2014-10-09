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
 * TableTemplate
 *
 * @category Generator
 * @package  Bluzman
 *
 * @author   Pavel Machekhin
 * @created  04/16/13 3:35 PM
 */

class TableTemplate extends AbstractTemplate
{
    /**
     * @return string
     */
    public function getTemplate()
    {
        $name = $this->options['name'];
        $table = $this->options['table'];

        // Primary key can be empty in options list
        if (!empty($this->options['primaryKey'])) {
            $primaryKey = '\'' . join('\',\'', $this->options['primaryKey']) . '\'';
        } else {
            $primaryKey = '';
        }


        $author = get_current_user();
        $date = date('Y-m-d H:i:s');

        return <<<EOF
<?php

/**
 * @namespace
 */
namespace Application\\$name;

/**
 * Table
 *
 * @category Application
 * @package  $name
 *
 * @author   $author
 * @created  $date
 */
class Table extends \Bluz\Db\Table
{
    /**
     * Table
     *
     * @var string
     */
    protected \$table = '$table';

    protected \$rowClass = '\Application\\$name\Row';

    /**
     * Primary key(s)
     * @var array
     */
    protected \$primary = array($primaryKey);
}
EOF;

    }
}