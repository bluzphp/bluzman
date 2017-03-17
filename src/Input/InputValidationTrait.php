<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/bluzman
 */

namespace Bluzman\Input;

use Respect\Validation;
use Respect\Validation\Exceptions\ValidationException;

/**
 * @package Bluzman\Input
 */
trait InputValidationTrait
{
    protected $messages = [
        'alnum' => '{{name}} must contain only letters and digits',
        'noWhitespace' => '{{name}} cannot contain spaces'
    ];

    /**
     * @var \Respect\Validation\Validator
     */
    protected $validator;

    /**
     * @param \Respect\Validation\Validator $validator
     */
    public function setValidator(Validation\Validator $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @return \Respect\Validation\Validator
     */
    public function getValidator()
    {
        return $this->validator;
    }

    /**
     * @param $value
     * @return bool
     * @throws \Exception
     */
    public function validate($value)
    {
        try {
            if ($this->getValidator()) {
                $this->getValidator()->check($value);
            }

            return true;
        } catch (ValidationException $e) {
            throw new InputException($e->getMainMessage());
        }

        return false;
    }
}
