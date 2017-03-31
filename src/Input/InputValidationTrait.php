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
        if (!$this->getValidator()) {
            return true;
        }

        try {
            return $this->getValidator()->assert($value);
        } catch (ValidatorException $e) {
            throw new InputException($e->getMessage());
        }
    }
}
