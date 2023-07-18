<?php

declare(strict_types=1);

namespace Retrofit\Tests\Core\Internal\ParameterHandler\Factory;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use Retrofit\Core\Attribute\GET;
use Retrofit\Core\Attribute\HeaderMap;
use Retrofit\Core\Internal\BuiltInConverterFactory;
use Retrofit\Core\Internal\ConverterProvider;
use Retrofit\Core\Internal\ParameterHandler\Factory\HeaderMapParameterHandlerFactory;
use Retrofit\Core\Internal\ParameterHandler\HeaderMapParameterHandler;
use Retrofit\Core\Type;
use Retrofit\Tests\Fixtures\Api\MockMethod;

class HeaderMapParameterHandlerFactoryTest extends TestCase
{
    private ReflectionMethod $reflectionMethod;

    private ConverterProvider $converterProvider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->reflectionMethod = new ReflectionMethod(MockMethod::class, 'mockMethod');
        $this->converterProvider = new ConverterProvider([new BuiltInConverterFactory()]);
    }

    #[Test]
    public function shouldCreateHeaderMapParameterHandler(): void
    {
        // given
        $headerMapParameterHandlerFactory = new HeaderMapParameterHandlerFactory($this->converterProvider);

        // when
        $parameterHandler = $headerMapParameterHandlerFactory->create(
            new HeaderMap(),
            new GET('/users/{login}'),
            null,
            $this->reflectionMethod,
            1,
            new Type('string'),
        );

        // then
        $this->assertInstanceOf(HeaderMapParameterHandler::class, $parameterHandler);
    }
}
