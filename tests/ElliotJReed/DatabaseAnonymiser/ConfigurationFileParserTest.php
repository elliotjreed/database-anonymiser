<?php

declare(strict_types=1);

namespace ElliotJReed\Tests\DatabaseAnonymiser;

use ElliotJReed\DatabaseAnonymiser\ConfigurationFileParser;
use ElliotJReed\DatabaseAnonymiser\Exceptions\UnsupportedConfigurationFile;
use PHPUnit\Framework\TestCase;
use SplFileObject;

final class ConfigurationFileParserTest extends TestCase
{
    public function testItParsesPhpFile(): void
    {
        $file = new SplFileObject('/tmp/test.php', 'wb+');
        $file->fwrite("<?php\nreturn ['field' => 'value'];");
        $file->rewind();

        $this->assertSame(['field' => 'value'], (new ConfigurationFileParser($file))->toArray());
    }

    public function testItParsesJsonFile(): void
    {
        $file = new SplFileObject('/tmp/test.json', 'wb+');
        $file->fwrite('{"field":"value"}');
        $file->rewind();

        $this->assertSame(['field' => 'value'], (new ConfigurationFileParser($file))->toArray());
    }

    public function testItParsesYamlFile(): void
    {
        $file = new SplFileObject('/tmp/test.yaml', 'wb+');
        $file->fwrite('field: value');
        $file->rewind();

        $this->assertSame(['field' => 'value'], (new ConfigurationFileParser($file))->toArray());
    }

    public function testItParsesYmlFile(): void
    {
        $file = new SplFileObject('/tmp/test.yml', 'wb+');
        $file->fwrite('field: value');
        $file->rewind();

        $this->assertSame(['field' => 'value'], (new ConfigurationFileParser($file))->toArray());
    }

    public function testItThrowsExceptionOnUnknownConfigurationFileType(): void
    {
        $this->expectException(UnsupportedConfigurationFile::class);

        $file = new SplFileObject('/tmp/test.ext', 'w+');
        (new ConfigurationFileParser($file))->toArray();
    }
}
