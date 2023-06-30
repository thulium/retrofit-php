<?php
declare(strict_types=1);

namespace Retrofit\Tests\Fixtures\Api;

use Retrofit\Attribute\GET;
use Retrofit\Attribute\HTTP;
use Retrofit\Attribute\Url;
use Retrofit\HttpMethod;

interface InvalidMethods
{
    public function withoutHttpAttribute();

    #[GET('/users')]
    #[HTTP(httpMethod: HttpMethod::POST, path: '/users', hasBody: true)]
    public function multipleHttpAttribute();

    #[GET]
    public function multipleUrlAttributes(#[Url] string $url1, #[Url] string $url2);
}
