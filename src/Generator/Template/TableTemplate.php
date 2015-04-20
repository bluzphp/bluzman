<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/bluzman
 */

namespace Bluzman\Generator\Template;

/**
 * TableTemplate
 *
 * @category Generator
 * @package  Bluzman
 *
 * @author   Pavel Machekhin
 * @created  2013-04-16 13:35
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
