<?php

declare(strict_types=1);

namespace Retrofit\Tests\Core\Internal\ParameterHandler\Factory;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use Retrofit\Core\Attribute\GET;
use Retrofit\Core\Attribute\Query;
use Retrofit\Core\Internal\BuiltInConverterFactory;
use Retrofit\Core\Internal\ConverterProvider;
use Retrofit\Core\Internal\ParameterHandler\Factory\QueryParameterHandlerFactory;
use Retrofit\Core\Internal\ParameterHandler\QueryParameterHandler;
use Retrofit\Core\Type;
use Retrofit\Tests\Fixtures\Api\MockMethod;

class QueryParameterHandlerFactoryTest extends TestCase
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
    public function shouldCreateQueryParameterHandler(): void
    {
        //given
        $queryParameterHandlerFactory = new QueryParameterHandlerFactory($this->converterProvider);

        //when
        $parameterHandler = $queryParameterHandlerFactory->create(
            new Query('group'),
            new GET('/users/{login}'),
            null,
            $this->reflectionMethod,
            1,
            new Type('string'),
        );

        //then
        $this->assertInstanceOf(QueryParameterHandler::class, $parameterHandler);
    }
}
