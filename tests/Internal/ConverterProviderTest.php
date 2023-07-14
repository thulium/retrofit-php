<?php
declare(strict_types=1);

namespace Retrofit\Tests\Internal;

use Ouzo\Tests\CatchException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Retrofit\Converter\Converter;
use Retrofit\Converter\StringConverter;
use Retrofit\Internal\BuiltInConverterFactory;
use Retrofit\Internal\ConverterProvider;
use Retrofit\Type;
use RuntimeException;

class ConverterProviderTest extends TestCase
{
    #[Test]
    public function shouldGetBuildInStringConverterWhenAnyOtherFound(): void
    {
        //given
        $converterProvider = new ConverterProvider([new BuiltInConverterFactory()]);

        //when
        $converter = $converterProvider->getStringConverter(new Type('int'));

        //then
        $this->assertInstanceOf(Converter::class, $converter);
        $this->assertInstanceOf(StringConverter::class, $converter);
        $this->assertStringStartsWith('Retrofit\Converter\StringConverter@anonymous', $converter::class);
        $this->assertStringContainsString('src/Internal/BuiltInConverters.php', $converter::class);
    }

    #[Test]
    public function shouldThrowExceptionWhenStringConverterNotFound(): void
    {
        //given
        $converterProvider = new ConverterProvider([]);

        //when
        CatchException::when($converterProvider)->getStringConverter(new Type('string'));

        //then
        CatchException::assertThat()
            ->isInstanceOf(RuntimeException::class)
            ->hasMessage('Cannot find string converter.');
    }

    #[Test]
    public function shouldThrowExceptionWhenResponseBodyConverterNotFound(): void
    {
        //given
        $converterProvider = new ConverterProvider([]);

        //when
        CatchException::when($converterProvider)->getResponseBodyConverter(new Type('array', 'string'));

        //then
        CatchException::assertThat()
            ->isInstanceOf(RuntimeException::class)
            ->hasMessage("Cannot find response body converter for type 'array<string>'.");
    }

    #[Test]
    public function shouldThrowExceptionWhenRequestBodyConverterNotFound(): void
    {
        //given
        $converterProvider = new ConverterProvider([]);

        //when
        CatchException::when($converterProvider)->getRequestBodyConverter(new Type('string'));

        //then
        CatchException::assertThat()
            ->isInstanceOf(RuntimeException::class)
            ->hasMessage("Cannot find request body converter for type 'string'.");
    }
}
