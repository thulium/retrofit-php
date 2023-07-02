<?php
declare(strict_types=1);

namespace Retrofit\Tests\Internal;

use GuzzleHttp\Psr7\Uri;
use Ouzo\Tests\CatchException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use Retrofit\Attribute\GET;
use Retrofit\Attribute\POST;
use Retrofit\Internal\RequestBuilder;
use RuntimeException;

class RequestBuilderTest extends TestCase
{
    #[Test]
    public function shouldCreateRequest(): void
    {
        //given
        $requestBuilder = new RequestBuilder(new Uri('https://example.com'), new GET('/users'));

        //when
        $request = $requestBuilder->build();

        //then
        $this->assertSame('GET', $request->getMethod());
        $this->assertSame('https://example.com/users', $request->getUri()->__toString());
    }

    #[Test]
    #[TestWith(['https://foo.bar/api/users'])]
    #[TestWith([new Uri('https://foo.bar/api/users')])]
    public function shouldSetNewBaseUrl(Uri|string $uri): void
    {
        //given
        $requestBuilder = new RequestBuilder(new Uri('https://example.com'), new GET);

        //when
        $requestBuilder->setBaseUrl($uri);

        //then
        $request = $requestBuilder->build();
        $this->assertSame('https://foo.bar/api/users', $request->getUri()->__toString());
    }

    #[Test]
    public function shouldReplacePathParameter(): void
    {
        //given
        $requestBuilder = new RequestBuilder(new Uri('https://example.com'), new GET('/users/{id}'));

        //when
        $requestBuilder->addPathParam('id', '1', false);

        //then
        $request = $requestBuilder->build();
        $this->assertSame('https://example.com/users/1', $request->getUri()->__toString());
    }

    #[Test]
    public function shouldReplaceEncodedPathParameter(): void
    {
        //given
        $requestBuilder = new RequestBuilder(new Uri('https://example.com'), new GET('/users/{login}'));

        //when
        $requestBuilder->addPathParam('login', 'Jon+Doe', true);

        //then
        $request = $requestBuilder->build();
        $this->assertSame('https://example.com/users/Jon%2BDoe', $request->getUri()->__toString());
    }

    #[Test]
    public function shouldReplaceMultiplePathParameters(): void
    {
        //given
        $requestBuilder = new RequestBuilder(new Uri('https://example.com'), new GET('/users/{login}/tickets/{id}'));

        //when
        $requestBuilder->addPathParam('login', 'Jon+Doe', false);
        $requestBuilder->addPathParam('id', '1', false);

        //then
        $request = $requestBuilder->build();
        $this->assertSame('https://example.com/users/Jon+Doe/tickets/1', $request->getUri()->__toString());
    }

    #[Test]
    public function shouldThrowExceptionWhenPathNameDoesNotPresentInUrl(): void
    {
        //given
        $requestBuilder = new RequestBuilder(new Uri('https://example.com'), new POST('/users/{login}'));

        //when
        $requestBuilder->addPathParam('not-matching', 'joe', false);
        CatchException::when($requestBuilder)->build();

        //then
        CatchException::assertThat()
            ->isInstanceOf(RuntimeException::class)
            ->hasMessage("URL '/users/{login}' does not contain 'not-matching'.");
    }

    #[Test]
    public function shouldSetNewBaseUrlAndReplacePathParameters(): void
    {
        //given
        $requestBuilder = new RequestBuilder(new Uri('https://example.com'), new GET);

        //when
        $requestBuilder->addPathParam('login', 'Jon+Doe', false);
        $requestBuilder->setBaseUrl('https://foo.bar/api/users/{login}');

        //then
        $request = $requestBuilder->build();
        $this->assertSame('https://foo.bar/api/users/Jon+Doe', $request->getUri()->__toString());
    }

    #[Test]
    public function shouldAddQueryParameter(): void
    {
        //given
        $requestBuilder = new RequestBuilder(new Uri('https://example.com'), new GET);

        //when
        $requestBuilder->addQueryParam('groups', 'new,old', false);

        //then
        $request = $requestBuilder->build();
        $this->assertSame('https://example.com?groups=new,old', $request->getUri()->__toString());
    }

    #[Test]
    public function shouldAddEncodedQueryParameter(): void
    {
        //given
        $requestBuilder = new RequestBuilder(new Uri('https://example.com'), new GET);

        //when
        $requestBuilder->addQueryParam('groups', 'new,old', true);

        //then
        $request = $requestBuilder->build();
        $this->assertSame('https://example.com?groups=new%2Cold', $request->getUri()->__toString());
    }

    #[Test]
    public function shouldAddArrayOfQueryParameters(): void
    {
        //given
        $requestBuilder = new RequestBuilder(new Uri('https://example.com'), new GET);

        //when
        $requestBuilder->addQueryParam('groups', ['new', 'old'], false);

        //then
        $request = $requestBuilder->build();
        $this->assertSame('https://example.com?groups=new&groups=old', $request->getUri()->__toString());
    }

    #[Test]
    public function shouldAddEncodedArrayOfQueryParameters(): void
    {
        //given
        $requestBuilder = new RequestBuilder(new Uri('https://example.com'), new GET);

        //when
        $requestBuilder->addQueryParam('groups', ['new+users', 'old'], true);

        //then
        $request = $requestBuilder->build();
        $this->assertSame('https://example.com?groups=new%2Busers&groups=old', $request->getUri()->__toString());
    }
}
