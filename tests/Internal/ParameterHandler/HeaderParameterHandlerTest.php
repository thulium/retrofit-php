<?php
declare(strict_types=1);

namespace Retrofit\Tests\Internal\ParameterHandler;

use GuzzleHttp\Psr7\Uri;
use Ouzo\Tests\Assert;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Retrofit\Attribute\GET;
use Retrofit\Internal\BuiltInConverters;
use Retrofit\Internal\ParameterHandler\HeaderParameterHandler;
use Retrofit\Internal\RequestBuilder;

class HeaderParameterHandlerTest extends TestCase
{
    private RequestBuilder $requestBuilder;

    public function setUp(): void
    {
        parent::setUp();
        $this->requestBuilder = new RequestBuilder(new Uri('https://example.com'), new GET('/users'));
    }

    #[Test]
    public function shouldSkipNullValues(): void
    {
        //given
        $headerParameterHandler = new HeaderParameterHandler('x-custom', BuiltInConverters::ToStringConverter());

        //when
        $headerParameterHandler->apply($this->requestBuilder, null);

        //then
        $request = $this->requestBuilder->build();
        Assert::thatArray($request->getHeaders())->containsKeyAndValue([]);
    }

    #[Test]
    public function shouldAddHeader(): void
    {
        //given
        $headerParameterHandler = new HeaderParameterHandler('x-custom', BuiltInConverters::ToStringConverter());

        //when
        $headerParameterHandler->apply($this->requestBuilder, 'value');

        //then
        $request = $this->requestBuilder->build();
        Assert::thatArray($request->getHeaders())->containsKeyAndValue(['x-custom' => ['value']]);
    }
}
