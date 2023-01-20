<?php

declare(strict_types=1);

namespace ElliotJReed\DatabaseAnonymiser\Exceptions;

final class ConfigurationFile extends \Exception
{
    protected $message = 'Invalid configuration.';
}
