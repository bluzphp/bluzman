<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/bluzman
 */

namespace Bluzman\Validation\Exceptions;

use Respect\Validation\Exceptions\ValidationException;

/**
 * @author  Pavel Machekhin
 * @created 2014-01-04 00:35
 */
class DirectoryEmptyException extends ValidationException
{
    public static $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD => '{{name}} must be an empty directory',
        ],
        self::MODE_NEGATIVE => [
            self::STANDARD => '{{name}} must not be an empty directory',
        ]
    ];
}

