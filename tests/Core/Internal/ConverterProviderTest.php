<?php

declare(strict_types=1);

namespace Retrofit\Tests\Core\Internal;

use Ouzo\Tests\CatchException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Retrofit\Core\Converter\Converter;
use Retrofit\Core\Converter\StringConverter;
use Retrofit\Core\Internal\BuiltInConverterFactory;
use Retrofit\Core\Internal\ConverterProvider;
use Retrofit\Core\Type;
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
        $this->assertStringStartsWith('Retrofit\Core\Converter\StringConverter@anonymous', $converter::class);
        $this->assertStringContainsString('src/Core/Internal/BuiltInConverters.php', $converter::class);
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
