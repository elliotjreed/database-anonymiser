<?php

declare(strict_types=1);

namespace ElliotJReed\DatabaseAnonymiser;

use ElliotJReed\DatabaseAnonymiser\Exceptions\UnsupportedConfigurationFile;
use SplFileObject;

class ConfigurationFileParser
{
    /**
     * ConfigurationFileParser constructor.
     * @param SplFileObject $file
     */
    public function __construct(private SplFileObject $file)
    {
    }

    /**
     * @return array An array of database configuration values and database tables to anonymise
     * @throws UnsupportedConfigurationFile
     */
    public function toArray(): array
    {
        $extension = $this->file->getExtension();
        if ($extension === 'php') {
            return require $this->file->getRealPath();
        }

        if ($extension === 'json') {
            return \json_decode($this->file->fread($this->file->getSize()), true);
        }

        if ($extension === 'yml' || $extension === 'yaml') {
            return \yaml_parse_file($this->file->getRealPath());
        }

        throw new UnsupportedConfigurationFile();
    }
}
