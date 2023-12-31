<?php

declare(strict_types=1);

namespace Retrofit\Tests\Core\Internal;

use GuzzleHttp\Psr7\Uri;
use Ouzo\Tests\Mock\Mock;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Retrofit\Core\Attribute\GET;
use Retrofit\Core\Internal\ParameterHandler\ParameterHandler;
use Retrofit\Core\Internal\RequestBuilder;
use Retrofit\Core\Internal\RequestFactory;

class RequestFactoryTest extends TestCase
{
    #[Test]
    public function shouldApplyParameterHandlers(): void
    {
        // given
        $loginParameterHandler = Mock::create(ParameterHandler::class);
        $idParameterHandler = Mock::create(ParameterHandler::class);
        $parameterHandlers = [$loginParameterHandler, $idParameterHandler];

        $requestFactory = new RequestFactory(new Uri('https://example.com'), new GET('/users/{login}/tickets/{id}'), [], $parameterHandlers);

        $args = ['Jon+Doe', 1];

        // when
        $requestFactory->create($args);

        // then
        Mock::verify($loginParameterHandler)->apply(Mock::argThat()->isInstanceOf(RequestBuilder::class), 'Jon+Doe');
        Mock::verify($idParameterHandler)->apply(Mock::argThat()->isInstanceOf(RequestBuilder::class), '1');
    }
}
