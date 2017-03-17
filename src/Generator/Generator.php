<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/bluzman
 */

namespace Bluzman\Generator;

use Bluz\View\View;
use Bluzman\Generator\Template\AbstractTemplate;
use Bluzman\Generator\Template\DummyTemplate;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Generator
 *
 * @category Generator
 * @package  Bluzman
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
    public function setTemplate($template)
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
    public function setAbsolutePath($absolutePath)
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
    public function setFs($fs)
    {
        $this->fs = $fs;
    }

    public function __construct(AbstractTemplate $template)
    {
        $this->setTemplate($template);
        $this->setAbsolutePath(__DIR__);
        $this->setFs(new Filesystem());
    }

    public function getCompiledTemplate()
    {
        $view = new View();
        $view->setPath($this->getAbsolutePath());
        $view->setTemplate($this->getTemplate()->getTemplatePath());
        $view->setFromArray($this->getTemplate()->getTemplateData());

        return $view->render();
    }

    /**
     * @param $path
     */
    public function make()
    {
        $this->getFs()->dumpFile(
            $this->getTemplate()->getFilePath(),
            $this->getCompiledTemplate()
        );
    }
}