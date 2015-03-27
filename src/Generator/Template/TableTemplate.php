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
     * @var string
     */
    protected $templatePath = 'views/table.html';

    /**
     * @param array $templateData
     */
    public function setTemplateData($templateData)
    {
        // Primary key can be empty in options list
        if (!empty($templateData['primaryKey'])) {
            $primaryKey = '\'' . join('\',\'', $templateData['primaryKey']) . '\'';
        } else {
            $primaryKey = '';
        }

        $templateData['primaryKey'] = $primaryKey;
        $this->templateData = $templateData;
    }
}
