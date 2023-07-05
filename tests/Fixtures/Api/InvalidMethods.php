<?php
declare(strict_types=1);

namespace Retrofit\Tests\Fixtures\Api;

use Retrofit\Attribute\FormUrlEncoded;
use Retrofit\Attribute\GET;
use Retrofit\Attribute\Headers;
use Retrofit\Attribute\HTTP;
use Retrofit\Attribute\Multipart;
use Retrofit\Attribute\Path;
use Retrofit\Attribute\Url;
use Retrofit\Call;
use Retrofit\HttpMethod;

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

    #[GET('/users')]
    #[FormUrlEncoded]
    #[Multipart]
    public function multipleEncodings(): Call;
}
