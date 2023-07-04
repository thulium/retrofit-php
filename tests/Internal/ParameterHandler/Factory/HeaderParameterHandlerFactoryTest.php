<?php
declare(strict_types=1);

namespace Retrofit\Tests\Internal\ParameterHandler\Factory;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use Retrofit\Attribute\GET;
use Retrofit\Attribute\Header;
use Retrofit\Internal\BuiltInConverterFactory;
use Retrofit\Internal\ConverterProvider;
use Retrofit\Internal\ParameterHandler\Factory\HeaderParameterHandlerFactory;
use Retrofit\Internal\ParameterHandler\HeaderParameterHandler;
use Retrofit\Tests\Fixtures\Api\MockMethod;

class HeaderParameterHandlerFactoryTest extends TestCase
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
    public function shouldCreateHeaderParameterHandler(): void
    {
        //given
        $headerParameterHandlerFactory = new HeaderParameterHandlerFactory($this->converterProvider);

        //when
        $parameterHandler = $headerParameterHandlerFactory->create(new Header('x-custom'), new GET('/users/{login}'), $this->reflectionMethod, 1);

        //then
        $this->assertInstanceOf(HeaderParameterHandler::class, $parameterHandler);
    }
}
