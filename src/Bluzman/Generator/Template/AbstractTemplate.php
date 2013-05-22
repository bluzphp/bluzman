<?php

namespace Bluzman\Generator\Template;

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
     * Must be redeclared and contain a text of the file to generate.
     *
     * @return mixed
     */
    abstract public function getTemplate();

    /**
     * @param $name
     * @param $path
     * @param array $options
     */
    final public function __construct($name, $path, $options = array())
    {
        if (!is_dir($path)) {
            throw new \RuntimeException('Directory "' . $path . '" not exists.');
        }

        if (!is_writable($path)) {
            throw new \RuntimeException('Directory "' . $path . '" not writable.');
        }

        $this->name = $name;
        $this->path = $path;
        $this->options = array_merge($options, $this->options);
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
            throw new Exception\AlreadyExistsException($this->name);
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