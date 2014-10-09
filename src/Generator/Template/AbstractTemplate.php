<?php

namespace Bluzman\Generator\Template;

use Bluzman\Generator\Template\Exception;

/**
 * AbstractTemplate
 *
 * @category Generator
 * @package  Bluzman
 *
 * @author   Pavel Machekhin
 * @created  3/28/13 4:36 PM
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
    public function setTemplatePath($templatePath)
    {
        $this->templatePath = $templatePath;
    }

    /**
     * @return array
     */
    public function getTemplateData()
    {
        return array_merge($this->getDefaultTemplateData(), $this->templateData);
    }

    /**
     * @param array $templateData
     */
    public function setTemplateData($templateData)
    {
        $this->templateData = $templateData;
    }

    /**
     * @return string
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * @param string $filePath
     */
    public function setFilePath($filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * Get the name of current user
     *
     * @return string
     */
    public function getAuthor()
    {
        return get_current_user();
    }

    /**
     * @return array
     */
    public function getDefaultTemplateData()
    {
        $date = new \DateTime();

        return [
            'author' => $this->getAuthor(),
            'date' => $date->format('Y-m-d H:i:s')
        ];
    }
}