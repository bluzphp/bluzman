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
abstract class AbstractTemplate implements TemplateInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var array
     */
    protected $options = array();

    /**
     * @param $name
     * @param $path
     * @param array $options
     */
    final public function __construct($name, $path, $options = array())
    {
        $this->setName($name);
        $this->setPath($path);
        $this->setOptions($options);
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param $path
     * @throws \RuntimeException
     */
    public function setPath($path)
    {
        if (!is_dir($path)) {
            throw new \RuntimeException('Directory "' . $path . '" not exists.');
        }

        if (!is_writable($path)) {
            throw new \RuntimeException('Directory "' . $path . '" not writable.');
        }

        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param array $options
     */
    public function setOptions($options)
    {
        $this->options = $options;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Checks the existence of the file and puts into a content of template.
     * @param bool $rewrite
     * @return mixed
     * @throws Exception\AlreadyExistsException
     */
    public function generate($rewrite = false)
    {
        $filepath = $this->path . DIRECTORY_SEPARATOR . $this->name;

        if (is_file($filepath)) {
            if ($rewrite) {
                $this->clean($filepath);

                return $this->generate(false);
            }
            throw new AlreadyExistsException($this->name);
        } else {
            if (!is_writable($filepath)) {
                throw new NotWritableException($this->name);
            }
        }

        file_put_contents($filepath, $this->getTemplate());
    }

    /**
     * Remove previously generated file.
     *
     * @param $filepath
     * @throws \RuntimeException
     */
    protected function clean($filepath)
    {
        if (!is_file($filepath)) {
            throw new \RuntimeException('Unable to remove "' . $filepath . '". File is not exist.');
        }
        unlink($filepath);
    }
}