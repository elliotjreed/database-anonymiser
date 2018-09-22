<?php
declare(strict_types=1);

namespace ElliotJReed\Tests\DatabaseAnonymiser;

use ElliotJReed\DatabaseAnonymiser\ConfigurationFileParser;
use ElliotJReed\DatabaseAnonymiser\Exceptions\UnsupportedConfigurationFile;
use PHPUnit\Framework\TestCase;
use SplFileObject;

class ConfigurationFileParserTest extends TestCase
{
    /**
     * @return void
     */
    public function testItParsesPhpFile(): void
    {
        (new SplFileObject('/tmp/test.php', 'w'))->fwrite("<?php\nreturn ['field' => 'value'];");
        $file = new SplFileObject('/tmp/test.php', 'r');

        $this->assertSame(['field' => 'value'], (new ConfigurationFileParser($file))->toArray());
    }

    /**
     * @return void
     */
    public function testItParsesJsonFile(): void
    {
        (new SplFileObject('/tmp/test.json', 'w+'))->fwrite('{"field":"value"}');
        $file = new SplFileObject('/tmp/test.json', 'r');

        $this->assertSame(['field' => 'value'], (new ConfigurationFileParser($file))->toArray());
    }

    /**
     * @return void
     */
    public function testItParsesYamlFile(): void
    {
        (new SplFileObject('/tmp/test.yaml', 'w'))->fwrite('field: value');
        $file = new SplFileObject('/tmp/test.yaml', 'r');

        $this->assertSame(['field' => 'value'], (new ConfigurationFileParser($file))->toArray());
    }

    /**
     * @return void
     */
    public function testItParsesYmlFile(): void
    {
        (new SplFileObject('/tmp/test.yml', 'w'))->fwrite('field: value');
        $file = new SplFileObject('/tmp/test.yml', 'r');

        $this->assertSame(['field' => 'value'], (new ConfigurationFileParser($file))->toArray());
    }

    /**
     * @return void
     */
    public function testItThrowsExceptionOnUnknownConfigurationFileType(): void
    {
        $this->expectException(UnsupportedConfigurationFile::class);

        $file = new SplFileObject('/tmp/test.ext', 'w+');
        (new ConfigurationFileParser($file))->toArray();
    }
}
