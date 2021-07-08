<?php

/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/bluzman
 */

namespace Bluzman\Generator\Template;

/**
 * AbstractTemplate
 *
 * @package  Bluzman\Generator\Template
 *
 * @todo     Migrate to Bluz\View\View
 * @author   Pavel Machekhin
 * @created  2013-03-28 16:36
 */
abstract class AbstractTemplate
{
    /**
     * @var string
     */
    protected $templatePath;

    /**
     * @var array
     */
    protected $templateData = [];

    /**
     * @var string
     */
    protected $filePath;

    /**
     * @return string
     */
    public function getTemplatePath()
    {
        return $this->templatePath;
    }

    /**
     * @param string $templatePath
     */
    public function setTemplatePath(string $templatePath)
    {
        $this->templatePath = $templatePath;
    }

    /**
     * @return array
     */
    public function getTemplateData(): array
    {
        return array_merge($this->getDefaultTemplateData(), $this->templateData);
    }

    /**
     * @param array $templateData
     */
    public function setTemplateData(array $templateData)
    {
        $this->templateData = $templateData;
    }

    /**
     * @return string
     */
    public function getFilePath(): string
    {
        return $this->filePath;
    }

    /**
     * @param string $filePath
     */
    public function setFilePath(string $filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * Get the name of current user
     *
     * @return string
     */
    public function getAuthor(): string
    {
        return get_current_user();
    }

    /**
     * @return array
     */
    public function getDefaultTemplateData(): array
    {
        $date = new \DateTime();

        return [
            'author' => $this->getAuthor(),
            'date' => $date->format('Y-m-d H:i:s')
        ];
    }
}
