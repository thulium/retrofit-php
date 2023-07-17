<?php
declare(strict_types=1);

namespace Retrofit\Tests\Internal\Utils;

use Ouzo\Tests\Assert;
use Ouzo\Utilities\Strings;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\IgnoreMethodForCodeCoverage;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;
use Retrofit\Internal\Utils\Utils;
use Retrofit\Tests\Fixtures\Api\FullyValidApi;

#[IgnoreMethodForCodeCoverage(Utils::class, '__construct')]
class UtilsTest extends TestCase
{
    #[Test]
    public function shouldReturnFQCN(): void
    {
        //given
        $className = 'Namespace\To\Class';

        //when
        $toFQCN = Utils::toFQCN($className);

        //then
        $this->assertSame('\Namespace\To\Class', $toFQCN);
    }

    #[Test]
    public function shouldNotDuplicateLeadingBackslashWhenIsExists(): void
    {
        //given
        $className = '\Namespace\To\Class';

        //when
        $toFQCN = Utils::toFQCN($className);

        //then
        $this->assertSame('\Namespace\To\Class', $toFQCN);
    }

    #[Test]
    public function shouldCreateFQCNUsingAllVarargs(): void
    {
        //given
        $name1 = '\Namespace';
        $name2 = 'To\Class';

        //when
        $toFQCN = Utils::toFQCN($name1, $name2);

        //then
        $this->assertSame('\Namespace\To\Class', $toFQCN);
    }

    #[Test]
    public function shouldReturnEmptyStringWhenFQCNParametersNotPass(): void
    {
        //when
        $toFQCN = Utils::toFQCN();

        //then
        $this->assertEmpty($toFQCN);
    }

    #[Test]
    public function shouldReturnExceptionWithMethodDetails(): void
    {
        //given
        $reflectionClass = new ReflectionClass(FullyValidApi::class);
        $reflectionMethod = $reflectionClass->getMethod('getInfo');

        //when
        $runtimeException = Utils::methodException($reflectionMethod, 'Some message to throw.');

        //then
        $this->assertSame('Method FullyValidApi::getInfo(). Some message to throw.', $runtimeException->getMessage());
    }

    #[Test]
    public function shouldReturnExceptionWithMethodAndParameterDetails(): void
    {
        //given
        $reflectionClass = new ReflectionClass(FullyValidApi::class);
        $reflectionMethod = $reflectionClass->getMethod('getInfo');

        //when
        $runtimeException = Utils::parameterException($reflectionMethod, 0, 'Some message to throw.');

        //then
        $this->assertSame('Method FullyValidApi::getInfo() parameter #1. Some message to throw.', $runtimeException->getMessage());
    }

    #[Test]
    public function shouldReturnEmptyArrayWhenThereAreNoParametersToParse(): void
    {
        //given
        $path = Strings::EMPTY;

        //when
        $pathParameters = Utils::parsePathParameters($path);

        //then
        $this->assertSame([], $pathParameters);
    }

    #[Test]
    public function shouldReturnEmptyArrayWhenPathIsNull(): void
    {
        //given
        $path = null;

        //when
        $pathParameters = Utils::parsePathParameters($path);

        //then
        $this->assertSame([], $pathParameters);
    }

    #[Test]
    #[DataProvider('pathParameters')]
    public function shouldParsePathParameters(string $path, $expectedPathParameters): void
    {
        //when
        $pathParameters = Utils::parsePathParameters($path);

        //then
        $this->assertSame($expectedPathParameters, $pathParameters);
    }

    public static function pathParameters(): array
    {
        return [
            ['/users/{id}', ['id']],
            ['/users/{login}/tickets/{id}', ['login', 'id']],
        ];
    }

    #[Test]
    public function shouldReturnOnlyUniqueParameters(): void
    {
        //given
        $path = '/users/{id}/tickets/{id}';

        //when
        $pathParameters = Utils::parsePathParameters($path);

        //then
        $this->assertSame(['id'], $pathParameters);
    }

    #[Test]
    public function shouldSortParameterAttributesUsingPriorities(): void
    {
        //given
        $reflectionMethod = new ReflectionMethod(FullyValidApi::class, 'pathIsBeforeUrl');
        $reflectionParameters = $reflectionMethod->getParameters();

        shuffle($reflectionParameters);
        shuffle($reflectionParameters);
        shuffle($reflectionParameters);

        //when
        $sortedReflectionParameters = Utils::sortParameterAttributesByPriorities($reflectionParameters);

        //then
        Assert::thatArray(array_values($sortedReflectionParameters))
            ->extracting(fn(ReflectionParameter $p): string => $p->getName())
            ->containsExactly('url', 'login');
    }
}
