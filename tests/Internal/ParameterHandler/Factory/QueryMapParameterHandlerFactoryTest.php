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
use Retrofit\Tests\Fixtures\Api\FullyValidApi;

class QueryMapParameterHandlerFactoryTest extends TestCase
{
    private ReflectionMethod $reflectionMethod;
    private ConverterProvider $converterProvider;

    public function setUp(): void
    {
        parent::setUp();
        $this->reflectionMethod = new ReflectionMethod(FullyValidApi::class, 'createUser');
        $this->converterProvider = new ConverterProvider([new BuiltInConverterFactory()]);
    }

    #[Test]
    public function shouldCreateQueryParameterHandler(): void
    {
        //given
        $queryMapParameterHandler = new QueryMapParameterHandlerFactory($this->converterProvider);

        //when
        $parameterHandler = $queryMapParameterHandler->create(new QueryMap(), new GET('/users'), $this->reflectionMethod, 1);

        //then
        $this->assertInstanceOf(QueryMapParameterHandler::class, $parameterHandler);
    }
}
