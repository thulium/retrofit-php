<?php

declare(strict_types=1);

namespace Retrofit\Tests\Fixtures\Api;

use Retrofit\Core\Attribute\FormUrlEncoded;
use Retrofit\Core\Attribute\GET;
use Retrofit\Core\Attribute\Headers;
use Retrofit\Core\Attribute\HTTP;
use Retrofit\Core\Attribute\Multipart;
use Retrofit\Core\Attribute\Path;
use Retrofit\Core\Attribute\POST;
use Retrofit\Core\Attribute\Url;
use Retrofit\Core\Call;
use Retrofit\Core\HttpMethod;

interface InvalidMethods
{
    public function withoutHttpAttribute(): Call;

    #[GET('/users')]
    #[HTTP(httpMethod: HttpMethod::POST, path: '/users', hasBody: true)]
    public function multipleHttpAttribute(): Call;

    #[GET]
    public function multipleUrlAttributes(#[Url] string $url1, #[Url] string $url2): Call;

    #[GET]
    public function urlAndPathSetTogether(#[Url] string $url, #[Path('name')] string $path): Call;

    #[GET('/users')]
    #[Headers([null => 'value'])]
    public function headersKeyIsNull(): Call;

    #[GET('/users')]
    #[Headers(['key' => null])]
    public function headersValueIsNull(): Call;

    #[POST('/users')]
    #[FormUrlEncoded]
    #[Multipart]
    public function multipleEncodings(): Call;

    #[GET('/users')]
    #[Multipart]
    public function multipartForHttpMethodWithoutBody(): Call;

    #[GET('/users')]
    #[FormUrlEncoded]
    public function formUrlEncodedForHttpMethodWithoutBody(): Call;

    #[POST('/users')]
    #[FormUrlEncoded]
    public function formUrlEncodedDoesNotHaveAtLeastOneFieldAttribute(): Call;

    #[POST('/users')]
    #[Multipart]
    public function multipartDoesNotHaveAtLeastOnePartAttribute(): Call;

    #[POST('/users')]
    public function parameterWithoutType($parameter): Call;

    #[POST('/users')]
    public function parameterWithoutAttribute(string $parameter): Call;

    #[POST('/users')]
    public function missingResponseBody(): Call;
}
