<?php
declare(strict_types=1);

namespace ElliotJReed\DatabaseAnonymiser\Exceptions;

use Exception;
use Throwable;

class ConfigurationFile extends Exception
{
    protected $message = 'Invalid configuration.';
}
