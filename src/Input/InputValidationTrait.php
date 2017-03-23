<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/bluzman
 */

namespace Bluzman\Input;

use Bluz\Validator\Validator;
use Bluz\Validator\Exception\ValidatorException;

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
     * @var Validator
     */
    protected $validator;

    /**
     * @param Validator $validator
     */
    public function setValidator(Validator $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @return Validator
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
                return $this->getValidator()->assert($value);
            }

            return true;
        } catch (ValidatorException $e) {
//            var_dump($e->getMessage());
//            var_dump($e->getCode());
            throw new InputException($e->getMessage());
        }
    }
}
