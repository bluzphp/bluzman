<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/bluzman
 */

namespace Bluzman\Generator\Template;

/**
 * TableTemplate
 *
 * @package  Bluzman\Generator\Template
 *
 * @author   Pavel Machekhin
 * @created  2013-04-16 13:35
 */

class TableTemplate extends AbstractTemplate
{
    /**
     * @var string
     */
    protected $templatePath = 'views/models/table.phtml';

    /**
     * @param array $templateData
     */
    public function setTemplateData($templateData)
    {
        // Primary key can be empty in options list
        $primaryKey = '';

        if (!empty($templateData['primaryKey'])) {
            $primaryKey = '\'' . implode('\',\'', $templateData['primaryKey']) . '\'';
        }

        $templateData['primaryKey'] = $primaryKey;
        $this->templateData = $templateData;
    }
}
