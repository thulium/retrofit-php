<?php
declare(strict_types=1);

namespace Retrofit\Tests\Internal\Proxy;

use Nyholm\Psr7\Uri;
use Ouzo\Tests\Mock\Mock;
use Ouzo\Tests\Mock\MockInterface;
use PhpParser\BuilderFactory;
use PhpParser\PrettyPrinter\Standard;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Retrofit\HttpClient;
use Retrofit\Internal\BuiltInConverters;
use Retrofit\Internal\ConverterProvider;
use Retrofit\Internal\Proxy\DefaultProxyFactory;
use Retrofit\Internal\Proxy\ProxyFactory;
use Retrofit\Retrofit;
use Retrofit\Tests\Fixtures\Api\ValidApi;

#[RunTestsInSeparateProcesses]
class DefaultProxyFactoryTest extends TestCase
{
    private HttpClient|MockInterface $httpClient;

    private ProxyFactory $defaultProxyFactory;

    public function setUp(): void
    {
        parent::setUp();
        $this->httpClient = Mock::create(HttpClient::class);

        $builderFactory = new BuilderFactory();
        $prettyPrinterAbstract = new Standard();
        $this->defaultProxyFactory = new DefaultProxyFactory($builderFactory, $prettyPrinterAbstract);
    }

    #[Test]
    public function shouldCreateImplementationWithImplSuffixInName(): void
    {
        //given
        $retrofit = new Retrofit($this->httpClient, new Uri(), new ConverterProvider([new BuiltInConverters()]), $this->defaultProxyFactory);

        //when
        $validApiImpl = $this->defaultProxyFactory->create($retrofit, new ReflectionClass(ValidApi::class));

        //then
        $reflectionClass = new ReflectionClass($validApiImpl);
        $this->assertSame('ValidApiImpl', $reflectionClass->getShortName());
    }

    #[Test]
    public function shouldCreateImplementationWithOneConstructorParameterWhichIsRetrofit(): void
    {
        //given
        $builderFactory = new BuilderFactory();
        $prettyPrinterAbstract = new Standard();

        $defaultProxyFactory = new DefaultProxyFactory($builderFactory, $prettyPrinterAbstract);

        /** @var HttpClient|MockInterface $httpClient */
        $httpClient = Mock::create(HttpClient::class);

        $retrofit = new Retrofit($httpClient, new Uri(), new ConverterProvider([new BuiltInConverters()]), $defaultProxyFactory);

        //when
        $validApiImpl = $defaultProxyFactory->create($retrofit, new ReflectionClass(ValidApi::class));

        //then
        $reflectionClass = new ReflectionClass($validApiImpl);
        $reflectionMethod = $reflectionClass->getConstructor();
        $reflectionParameters = $reflectionMethod->getParameters();
        $this->assertCount(1, $reflectionParameters);

        $reflectionParameter = $reflectionParameters[0];
        $this->assertSame(Retrofit::class, $reflectionParameter->getType()->getName());
        $this->assertSame('retrofit', $reflectionParameter->getName());
        $this->assertTrue($reflectionParameter->isPromoted());
        $this->assertFalse($reflectionParameter->allowsNull());
    }
}
