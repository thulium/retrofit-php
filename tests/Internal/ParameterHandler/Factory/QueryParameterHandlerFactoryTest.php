<?php
declare(strict_types=1);

namespace Retrofit\Tests\Internal\ParameterHandler\Factory;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use Retrofit\Attribute\GET;
use Retrofit\Attribute\Query;
use Retrofit\Internal\BuiltInConverterFactory;
use Retrofit\Internal\ConverterProvider;
use Retrofit\Internal\ParameterHandler\Factory\QueryParameterHandlerFactory;
use Retrofit\Internal\ParameterHandler\QueryParameterHandler;
use Retrofit\Tests\Fixtures\Api\FullyValidApi;

class QueryParameterHandlerFactoryTest extends TestCase
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
        $pathParameterHandlerFactory = new QueryParameterHandlerFactory($this->converterProvider);

        //when
        $parameterHandler = $pathParameterHandlerFactory->create(new Query('group'), new GET('/users/{login}'), $this->reflectionMethod, 1);

        //then
        $this->assertInstanceOf(QueryParameterHandler::class, $parameterHandler);
    }
}
