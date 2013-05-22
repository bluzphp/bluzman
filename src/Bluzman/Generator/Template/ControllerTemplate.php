<?php

namespace Bluzman\Generator\Template;

/**
 * ControllerTemplate
 *
 * @category Generator
 * @package  Bluzman
 *
 * @author   Pavel Machekhin
 * @created  3/28/13 4:33 PM
 */

class ControllerTemplate extends AbstractTemplate
{
    /**
     * @return string
     */
    public function getTemplate()
    {
        $author = get_current_user();
        $date = date('Y-m-d H:i:s');

        return <<<EOF
<?php

/**
 *
 * @author   $author
 * @created  $date
 */
namespace Application;

use Bluz;

return
/**
 * @return \closure
 */
function () use (\$view) {
    /**
     * @var \Bluz\Application \$this
     * @var \Bluz\View\View \$view
     */
    \$request = \$this->getRequest();
};
EOF;

    }
}