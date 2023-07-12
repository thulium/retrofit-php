<?php
declare(strict_types=1);

namespace Retrofit\Tests\Internal\ParameterHandler\Factory;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use Retrofit\Attribute\GET;
use Retrofit\Attribute\QueryMap;
use Retrofit\Internal\BuiltInConverterFactory;
use Retrofit\Internal\ConverterProvider;
use Retrofit\Internal\ParameterHandler\Factory\QueryMapParameterHandlerFactory;
use Retrofit\Internal\ParameterHandler\QueryMapParameterHandler;
use Retrofit\Tests\Fixtures\Api\MockMethod;
use Retrofit\Type;

class QueryMapParameterHandlerFactoryTest extends TestCase
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
    public function shouldCreateQueryMapParameterHandler(): void
    {
        //given
        $queryMapParameterHandler = new QueryMapParameterHandlerFactory($this->converterProvider);

        //when
        $parameterHandler = $queryMapParameterHandler->create(
            new QueryMap(), new GET('/users'), null, $this->reflectionMethod, 1, new Type('string')
        );

        //then
        $this->assertInstanceOf(QueryMapParameterHandler::class, $parameterHandler);
    }
}
