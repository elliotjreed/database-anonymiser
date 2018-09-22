<?php
declare(strict_types=1);

namespace ElliotJReed\DatabaseAnonymiser\Exceptions;

use Exception;

class UnsupportedConfigurationFile extends Exception
{
    protected $message = 'Configuration file must be JSON, YAML, or PHP.';
}
