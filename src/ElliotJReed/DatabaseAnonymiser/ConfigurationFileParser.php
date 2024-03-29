<?php

declare(strict_types=1);

namespace ElliotJReed\DatabaseAnonymiser;

use ElliotJReed\DatabaseAnonymiser\Exceptions\UnsupportedConfigurationFile;

class ConfigurationFileParser
{
    /**
     * ConfigurationFileParser constructor.
     */
    public function __construct(private \SplFileObject $file)
    {
    }

    /**
     * @return array An array of database configuration values and database tables to anonymise
     *
     * @throws UnsupportedConfigurationFile
     */
    public function toArray(): array
    {
        $extension = $this->file->getExtension();
        if ('php' === $extension) {
            return require $this->file->getRealPath();
        }

        if ('json' === $extension) {
            return \json_decode($this->file->fread($this->file->getSize()), true);
        }

        if ('yml' === $extension || 'yaml' === $extension) {
            return \yaml_parse_file($this->file->getRealPath());
        }

        throw new UnsupportedConfigurationFile();
    }
}
