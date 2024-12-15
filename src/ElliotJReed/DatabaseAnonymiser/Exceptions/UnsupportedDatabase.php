<?php

declare(strict_types=1);

namespace ElliotJReed\DatabaseAnonymiser\Exceptions;

use Exception;

final class UnsupportedDatabase extends Exception
{
    protected $message = 'Database driver is not supported.';
}
