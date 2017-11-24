<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/bluzman
 */

namespace Bluzman\Tests\Generator;

use Bluzman\Generator\Generator;
use Bluzman\Generator\Template\AbstractTemplate;
use Bluzman\Tests\BluzmanTestCase;
use Bluzman\Generator\Template\ControllerTemplate;

/**
 * @author Pavel Machekhin
 * @created 2014-08-10 13:36
 */
class GeneratorTest extends BluzmanTestCase
{
    public function testGetTemplateData()
    {
        $template = new DummyTemplate();
        $template->setTemplateData(
            [
                'variable' => 'bar'
            ]
        );

        $generator = new Generator($template);
        $generator->setAbsolutePath(__DIR__ . DS . 'fixtures');

        self::assertEmpty($generator->getTemplate()->getDefaultTemplateData());
        self::assertNotEmpty($generator->getTemplate()->getTemplateData());
    }

    /**
     * @dataProvider templateOptions
     */
    public function testCompiledTemplate($templateOptions)
    {
        $container = new \Mockery\Container;
        $faker = \Faker\Factory::create();
        $filePath = $this->workingPath . DS . $faker->lexify . '.' . $faker->fileExtension;

        /**
         * @var $template AbstractTemplate
         */
        $template = $container->mock($templateOptions['template'])
            ->shouldDeferMissing()
            ->shouldAllowMockingProtectedMethods();

        $template->shouldReceive('getDefaultTemplateData')
            ->atLeast(1)
            ->andReturn($templateOptions['defaultTemplateData']);

        $template->setTemplateData($templateOptions['templateData']);
        $template->setFilePath($filePath);

        $generator = new Generator($template);
        $generator->setAbsolutePath(__DIR__ . DS . 'fixtures');

        self::assertEquals(
            $templateOptions['stub'],
            $generator->getCompiledTemplate()
        );

        $generator->make();

        self::assertFileExists($filePath);
    }

    /**
     * @return array
     */
    public function templateOptions()
    {
        return [
            [
                [
                    'template' => DummyTemplate::class,
                    'defaultTemplateData' => [

                    ],
                    'templateData' => [
                        'variable' => 'bar'
                    ],
                    'stub' => 'foo=bar'
                ],
                [
                    'template' => ControllerTemplate::class,
                    'defaultTemplateData' => [
                        'author' => 'test',
                        'date' => '2000-01-01 23:59:59'
                    ],
                    'templateData' => [

                    ],
                    'stub' => file_get_contents(__DIR__ . DS . 'samples' . DS . 'controller.html')
                ]
            ]
        ];
    }
} 