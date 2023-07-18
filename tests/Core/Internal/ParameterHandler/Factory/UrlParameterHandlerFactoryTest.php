<?php

declare(strict_types=1);

namespace Retrofit\Tests\Core\Internal\ParameterHandler\Factory;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use Retrofit\Core\Attribute\POST;
use Retrofit\Core\Attribute\Url;
use Retrofit\Core\Internal\BuiltInConverterFactory;
use Retrofit\Core\Internal\ConverterProvider;
use Retrofit\Core\Internal\ParameterHandler\Factory\UrlParameterHandlerFactory;
use Retrofit\Core\Internal\ParameterHandler\UrlParameterHandler;
use Retrofit\Core\Type;
use Retrofit\Tests\Fixtures\Api\MockMethod;

class UrlParameterHandlerFactoryTest extends TestCase
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
    public function shouldCreateUrlParameterHandler(): void
    {
        //given
        $urlParameterHandlerFactory = new UrlParameterHandlerFactory($this->converterProvider);

        //when
        $parameterHandler = $urlParameterHandlerFactory->create(
            new Url(),
            new POST(),
            null,
            $this->reflectionMethod,
            0,
            new Type('string'),
        );

        //then
        $this->assertInstanceOf(UrlParameterHandler::class, $parameterHandler);
    }
}
