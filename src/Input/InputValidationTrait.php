<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/bluzman
 */

namespace Bluzman\Input;

use Bluz\Validator\Exception\ValidatorException;
use Bluz\Validator\ValidatorChain;

/**
 * @package Bluzman\Input
 */
trait InputValidationTrait
{
    /**
     * @var ValidatorChain
     */
    protected $validator;

    /**
     * @param ValidatorChain $validator
     */
    public function setValidator(ValidatorChain $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @return ValidatorChain
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
        if (!$this->getValidator()) {
            return true;
        }

        if ($this->getValidator()->validate($value)) {
            return true;
        }
        throw new InputException($this->getValidator()->getError());
    }
}
