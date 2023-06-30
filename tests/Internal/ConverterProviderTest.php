<?php
declare(strict_types=1);

namespace Retrofit\Tests\Internal;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Retrofit\Converter;
use Retrofit\Internal\ConverterProvider;

class ConverterProviderTest extends TestCase
{
    #[Test]
    public function shouldGetBuildInStringConverterWhenAnyOtherFound(): void
    {
        //given
        $converterProvider = new ConverterProvider([]);

        //when
        $converter = $converterProvider->getStringConverter();

        //then
        $this->assertInstanceOf(Converter::class, $converter);
        $this->assertStringStartsWith('Retrofit\Converter@anonymous', $converter::class);
        $this->assertStringContainsString('src/Internal/BuiltInConverters.php', $converter::class);
    }
}
