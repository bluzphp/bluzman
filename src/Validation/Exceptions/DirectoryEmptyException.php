<?php
/**
 * @author bashmach
 * @created 2014-01-04 00:35
 */

namespace Bluzman\Validation\Exceptions;

use Respect\Validation\Exceptions\ValidationException;

class DirectoryEmptyException extends ValidationException
{
    public static $defaultTemplates = array(
        self::MODE_DEFAULT => array(
            self::STANDARD => '{{name}} must be an empty directory',
        ),
        self::MODE_NEGATIVE => array(
            self::STANDARD => '{{name}} must not be an empty directory',
        )
    );
}

