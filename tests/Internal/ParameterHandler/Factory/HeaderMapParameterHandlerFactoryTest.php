<?php
declare(strict_types=1);

namespace Retrofit\Tests\Internal\ParameterHandler\Factory;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use Retrofit\Attribute\GET;
use Retrofit\Attribute\HeaderMap;
use Retrofit\Internal\BuiltInConverterFactory;
use Retrofit\Internal\ConverterProvider;
use Retrofit\Internal\ParameterHandler\Factory\HeaderMapParameterHandlerFactory;
use Retrofit\Internal\ParameterHandler\HeaderMapParameterHandler;
use Retrofit\Tests\Fixtures\Api\MockMethod;
use Retrofit\Type;

class HeaderMapParameterHandlerFactoryTest extends TestCase
{
    private ReflectionMethod $reflectionMethod;
    private ConverterProvider $converterProvider;

    public function setUp(): void
    {
        parent::setUp();
        $this->reflectionMethod = new ReflectionMethod(MockMethod::class, 'mockMethod');
        $this->converterProvider = new ConverterProvider([new BuiltInConverterFactory()]);
    }

    #[Test]
    public function shouldCreateHeaderMapParameterHandler(): void
    {
        //given
        $headerMapParameterHandlerFactory = new HeaderMapParameterHandlerFactory($this->converterProvider);

        //when
        $parameterHandler = $headerMapParameterHandlerFactory->create(
            new HeaderMap(), new GET('/users/{login}'), null, $this->reflectionMethod, 1, new Type('string')
        );

        //then
        $this->assertInstanceOf(HeaderMapParameterHandler::class, $parameterHandler);
    }
}
