<?php

namespace Bluzman\Input;

use Respect\Validation;

class InputArgument extends \Symfony\Component\Console\Input\InputArgument
{
    use InputValidationTrait;
}