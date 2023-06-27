<?php
declare(strict_types=1);

namespace Retrofit\Tests\Internal\ParameterHandler\Factory;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Retrofit\Attribute\GET;
use Retrofit\Attribute\Path;
use Retrofit\Internal\BuiltInConverters;
use Retrofit\Internal\ConverterProvider;
use Retrofit\Internal\ParameterHandler\Factory\PathParameterHandlerFactory;
use RuntimeException;

class PathParameterHandlerFactoryTest extends TestCase
{
    #[Test]
    public function shouldThrowExceptionWhenPathNameDoesNotPresentInUrl(): void
    {
        //given
        $pathParameterHandlerFactory = new PathParameterHandlerFactory(new GET('/user/{name}'), new ConverterProvider([new BuiltInConverters()]));

        //when
        try {
            $pathParameterHandlerFactory->create(new Path('not-matching'));
            //then
        } catch (RuntimeException $e) {
            $this->assertSame("URL '/user/{name}' does not contain 'not-matching'.", $e->getMessage());
        }
    }
}
