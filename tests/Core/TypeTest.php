<?php

declare(strict_types=1);

namespace Retrofit\Tests\Core;

use phpDocumentor\Reflection\DocBlock\Tags\Param;
use phpDocumentor\Reflection\DocBlockFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use ReflectionParameter;
use Retrofit\Core\Type;
use Retrofit\Tests\Fixtures\Api\TypeResolverApi;
use Retrofit\Tests\Fixtures\Model\UserRequest;

class TypeTest extends TestCase
{
    #[Test]
    #[DataProvider('scalarParameterTypes')]
    public function shouldCreateTypeForScalarParameterTypes(
        ReflectionMethod $reflectionMethod,
        ReflectionParameter $reflectionParameter,
        array $params,
        string $expectedRawType,
    ): void
    {
        // when
        $type = Type::create($reflectionMethod, $reflectionParameter, $params);

        // then
        $this->assertSame($expectedRawType, $type->getRawType());
    }

    #[Test]
    #[DataProvider('customClassParameterTypes')]
    public function shouldCreateTypeForCustomClassParameterType(
        ReflectionMethod $reflectionMethod,
        ReflectionParameter $reflectionParameter,
        array $params,
        string $expectedRawType,
    ): void
    {
        // when
        $type = Type::create($reflectionMethod, $reflectionParameter, $params);

        // then
        $this->assertSame($expectedRawType, $type->getRawType());
    }

    #[Test]
    #[DataProvider('genericClassParameterTypes')]
    public function shouldCreateTypeForGenericClassParameterType(
        ReflectionMethod $reflectionMethod,
        ReflectionParameter $reflectionParameter,
        array $params,
        string $expectedRawType,
    ): void
    {
        // when
        $type = Type::create($reflectionMethod, $reflectionParameter, $params);

        // then
        $this->assertSame($expectedRawType, $type->getRawType());
    }

    #[Test]
    #[DataProvider('arrayOfScalarParameterTypes')]
    /** @param Param[] $params */
    public function shouldCreateTypeForArrayOfScalarParameterType(
        ReflectionMethod $reflectionMethod,
        ReflectionParameter $reflectionParameter,
        array $params,
        string $expectedRawType,
    ): void
    {
        // when
        $type = Type::create($reflectionMethod, $reflectionParameter, $params);

        // then
        $this->assertSame($expectedRawType, $type->getRawType());
        $this->assertNotNull($type->getParametrizedType());
    }

    #[Test]
    #[DataProvider('arrayOfCustomClassParameterTypes')]
    /** @param Param[] $params */
    public function shouldCreateTypeForArrayOfCustomClassParameterType(
        ReflectionMethod $reflectionMethod,
        ReflectionParameter $reflectionParameter,
        array $params,
        string $expectedRawType,
    ): void
    {
        // when
        $type = Type::create($reflectionMethod, $reflectionParameter, $params);

        // then
        $this->assertSame($expectedRawType, $type->getRawType());
        $this->assertNotNull($type->getParametrizedType());
    }

    #[Test]
    #[DataProvider('arrayOfGenericClassParameterTypes')]
    /** @param Param[] $params */
    public function shouldCreateTypeForArrayOfGenericClassParameterType(
        ReflectionMethod $reflectionMethod,
        ReflectionParameter $reflectionParameter,
        array $params,
        string $expectedRawType,
    ): void
    {
        // when
        $type = Type::create($reflectionMethod, $reflectionParameter, $params);

        // then
        $this->assertSame($expectedRawType, $type->getRawType());
        $this->assertNotNull($type->getParametrizedType());
    }

    #[Test]
    public function shouldCheckParameterIsATypeOf(): void
    {
        // given
        $reflectionMethod = new ReflectionMethod(TypeResolverApi::class, 'customClass');

        $type = Type::create($reflectionMethod, $reflectionMethod->getParameters()[0]);

        // when
        $isA = $type->isA(UserRequest::class);

        // then
        $this->assertTrue($isA);
    }

    public static function scalarParameterTypes(): array
    {
        $reflectionMethod = new ReflectionMethod(TypeResolverApi::class, 'scalarTypes');
        return self::getParameterTypes($reflectionMethod);
    }

    public static function customClassParameterTypes(): array
    {
        $reflectionMethod = new ReflectionMethod(TypeResolverApi::class, 'customClass');
        return self::getParameterTypes($reflectionMethod);
    }

    public static function genericClassParameterTypes(): array
    {
        $reflectionMethod = new ReflectionMethod(TypeResolverApi::class, 'genericClass');
        return self::getParameterTypes($reflectionMethod);
    }

    public static function arrayOfScalarParameterTypes(): array
    {
        $reflectionMethod = new ReflectionMethod(TypeResolverApi::class, 'arrayOfScalarTypes');
        return self::getParameterTypes($reflectionMethod);
    }

    public static function arrayOfCustomClassParameterTypes(): array
    {
        $reflectionMethod = new ReflectionMethod(TypeResolverApi::class, 'arrayOfCustomClass');
        return self::getParameterTypes($reflectionMethod);
    }

    public static function arrayOfGenericClassParameterTypes(): array
    {
        $reflectionMethod = new ReflectionMethod(TypeResolverApi::class, 'arrayOfGenericClass');
        return self::getParameterTypes($reflectionMethod);
    }

    private static function getParameterTypes(ReflectionMethod $reflectionMethod): array
    {
        $result = [];

        $params = [];
        $docComment = $reflectionMethod->getDocComment();
        if ($docComment !== false) {
            $docBlockFactory = DocBlockFactory::createInstance();
            $docBlock = $docBlockFactory->create($docComment);
            $params = $docBlock->getTagsByName('param');
        }

        foreach ($reflectionMethod->getParameters() as $reflectionParameter) {
            $result[] = [
                $reflectionMethod,
                $reflectionParameter,
                $params,
                $reflectionParameter->getType()->getName(),
            ];
        }

        return $result;
    }
}
