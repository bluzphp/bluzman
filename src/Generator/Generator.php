<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/bluzman
 */

namespace Bluzman\Generator;

use Bluz\View\View;
use Bluzman\Generator\Template\AbstractTemplate;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Generator
 *
 * @package  Bluzman\Generator
 *
 * @author   Pavel Machekhin
 * @created  2013-03-28 16:24
 */
class Generator
{
    /**
     * @var AbstractTemplate
     */
    protected $template;

    /**
     * @var string
     */
    protected $absolutePath;

    /**
     * @var Filesystem
     */
    protected $fs;

    public function __construct(AbstractTemplate $template)
    {
        $this->setTemplate($template);
        $this->setAbsolutePath(__DIR__);
        $this->setFs(new Filesystem());
    }

    /**
     * @return AbstractTemplate
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param AbstractTemplate $template
     */
    public function setTemplate($template) : void
    {
        $this->template = $template;
    }

    /**
     * @return string
     */
    public function getAbsolutePath()
    {
        return $this->absolutePath;
    }

    /**
     * @param string $absolutePath
     */
    public function setAbsolutePath($absolutePath) : void
    {
        $this->absolutePath = $absolutePath;
    }

    /**
     * @return Filesystem
     */
    public function getFs()
    {
        return $this->fs;
    }

    /**
     * @param Filesystem $fs
     */
    public function setFs($fs) : void
    {
        $this->fs = $fs;
    }

    /**
     * @internal param $path
     */
    public function make() : void
    {
        $this->getFs()->dumpFile(
            $this->getTemplate()->getFilePath(),
            $this->getCompiledTemplate()
        );
    }

    public function getCompiledTemplate()
    {
        $view = new View();
        $view->setPath($this->getAbsolutePath());
        $view->setTemplate($this->getTemplate()->getTemplatePath());
        $view->setFromArray($this->getTemplate()->getTemplateData());

        return $view->render();
    }
}
