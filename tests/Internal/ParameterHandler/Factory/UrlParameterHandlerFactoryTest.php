<?php
declare(strict_types=1);

namespace Retrofit\Tests\Internal\ParameterHandler\Factory;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use Retrofit\Attribute\POST;
use Retrofit\Attribute\Url;
use Retrofit\Internal\BuiltInConverterFactory;
use Retrofit\Internal\ConverterProvider;
use Retrofit\Internal\ParameterHandler\Factory\UrlParameterHandlerFactory;
use Retrofit\Internal\ParameterHandler\UrlParameterHandler;
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
        $parameterHandler = $urlParameterHandlerFactory->create(new Url(), new POST(), null, $this->reflectionMethod, 0);

        //then
        $this->assertInstanceOf(UrlParameterHandler::class, $parameterHandler);
    }
}
