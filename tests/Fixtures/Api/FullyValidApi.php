<?php
declare(strict_types=1);

namespace Retrofit\Tests\Fixtures\Api;

use Psr\Http\Message\StreamInterface;
use Retrofit\Attribute\Body;
use Retrofit\Attribute\Field;
use Retrofit\Attribute\FieldMap;
use Retrofit\Attribute\FormUrlEncoded;
use Retrofit\Attribute\GET;
use Retrofit\Attribute\Header;
use Retrofit\Attribute\HeaderMap;
use Retrofit\Attribute\Headers;
use Retrofit\Attribute\Multipart;
use Retrofit\Attribute\Part;
use Retrofit\Attribute\PartMap;
use Retrofit\Attribute\Path;
use Retrofit\Attribute\POST;
use Retrofit\Attribute\Query;
use Retrofit\Attribute\QueryMap;
use Retrofit\Attribute\QueryName;
use Retrofit\Attribute\Response\ErrorBody;
use Retrofit\Attribute\Response\ResponseBody;
use Retrofit\Attribute\Streaming;
use Retrofit\Attribute\Url;
use Retrofit\Call;
use Retrofit\Multipart\PartInterface;
use Retrofit\Tests\Fixtures\Model\UserRequest;
use stdClass;

interface FullyValidApi
{
    #[GET('/info/{login}')]
    #[ResponseBody('string')]
    public function getInfo(#[Path('login')] string $login): Call;

    #[POST('/users/{login}')]
    #[ResponseBody('string')]
    public function createUser(#[Path('login')] string $login, #[Body] UserRequest $userRequest): Call;

    #[GET]
    #[ResponseBody('string')]
    public function pathIsBeforeUrl(#[Path('login')] string $login, #[Url] string $url): Call;

    #[GET]
    #[ResponseBody('string')]
    public function urlWithQuery(#[Query('group')] string $group, #[Url] string $url): Call;

    #[GET('/users/{login}')]
    #[ResponseBody('string')]
    public function pathAndQuery(#[Path('login')] string $login, #[Query('group')] string $group): Call;

    #[GET('/users')]
    #[ResponseBody('string')]
    public function addQueryName(#[QueryName(true)] string $queryName): Call;

    #[GET('/users')]
    #[ResponseBody('string')]
    public function addQueryMap(#[QueryMap] array $queries): Call;

    #[GET('/users')]
    #[ResponseBody('string')]
    public function addHeader(#[Header('x-custom')] string $custom): Call;

    #[GET('/users')]
    #[ResponseBody('string')]
    public function addHeaderMap(#[HeaderMap] array $headerMap): Call;

    #[GET('/users')]
    #[Headers([
        'x-custom' => 'jon+doe',
        'x-age' => 34,
        'Content-Type' => 'application/json',
    ])]
    #[ResponseBody('string')]
    public function addHeaders(): Call;

    #[GET('/users')]
    #[Headers([
        'x-custom' => 'jon+doe',
        'x-age' => 34,
        'Content-Type' => 'application/json',
    ])]
    #[ResponseBody('string')]
    public function addHeadersWithParameterHeader(#[Header('x-age')] int $age): Call;

    #[POST('/users/login')]
    #[FormUrlEncoded]
    #[ResponseBody('string')]
    public function formUrlEncoded(#[Path('login')] string $login, #[Field('filters', true)] string $filters): Call;

    #[POST('/users/login')]
    #[Multipart]
    #[ResponseBody('string')]
    public function multipart(#[Path('login')] string $login, #[Part] string $filters): Call;

    #[POST('/users')]
    #[FormUrlEncoded]
    #[ResponseBody('string')]
    public function addField(#[Field('x-login')] string $login, #[Field('filters', true)] string $filters): Call;

    #[POST('/users')]
    #[FormUrlEncoded]
    #[ResponseBody('string')]
    public function addFieldMap(#[FieldMap(true)] array $fields): Call;

    #[POST('/users')]
    #[Multipart]
    #[ResponseBody('string')]
    public function addPart(
        #[Part('string')] string $p1,
        #[Part('userRequest')] UserRequest $p2,
        #[Part] PartInterface $p3,
        #[Part('stream')] StreamInterface $p4
    ): Call;

    #[POST('/users')]
    #[Multipart]
    #[ResponseBody('string')]
    public function addPartMap(#[PartMap] array $partMap): Call;

    #[POST('/users')]
    #[ResponseBody('string')]
    public function setBody(#[Body] UserRequest $userRequest): Call;

    #[POST('/users')]
    #[ResponseBody('string')]
    public function returnScalar(#[Body] UserRequest $userRequest): Call;

    #[POST('/users')]
    #[ResponseBody('void')]
    public function returnVoid(#[Body] UserRequest $userRequest): Call;

    #[GET('/users')]
    #[ResponseBody(stdClass::class)]
    public function returnStdClass(): Call;

    #[GET('/users')]
    #[ResponseBody('array', 'string')]
    public function returnArrayOfScalar(): Call;

    #[GET('/users')]
    #[ResponseBody('array', stdClass::class)]
    public function returnArrayOfStdClass(): Call;

    #[GET('/users')]
    #[ResponseBody('array', stdClass::class)]
    #[ErrorBody(stdClass::class)]
    public function testErrorBody(): Call;

    #[GET('/users')]
    #[ResponseBody('array', stdClass::class)]
    public function testErrorBodyWithoutMapping(): Call;

    #[GET('/users')]
    #[Streaming]
    public function streamInterfaceAsResponseBody(): Call;
}
