<?php
declare(strict_types=1);

namespace Retrofit\Tests\Attribute;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Retrofit\Attribute\DELETE;
use Retrofit\Attribute\GET;
use Retrofit\Attribute\HEAD;
use Retrofit\Attribute\HTTP;
use Retrofit\Attribute\HttpRequest;
use Retrofit\Attribute\OPTIONS;
use Retrofit\Attribute\PATCH;
use Retrofit\Attribute\POST;
use Retrofit\Attribute\PUT;
use Retrofit\HttpMethod;

class HttpRequestTest extends TestCase
{
    #[Test]
    #[DataProvider('httpMethods')]
    public function shouldCheckHttpRequest(HttpRequest $httpRequest, HttpMethod $httpMethod, string $path, array $pathParameters, bool $hasBody): void
    {
        //then
        $this->assertSame($httpMethod, $httpRequest->httpMethod());
        $this->assertSame($path, $httpRequest->path());
        $this->assertSame($pathParameters, $httpRequest->pathParameters());
        $this->assertSame($hasBody, $httpRequest->hasBody());
    }

    public static function httpMethods(): array
    {
        return [
            [new DELETE('/users/{id}'), HttpMethod::DELETE, '/users/{id}', ['id'], false],
            [new GET('/users/{id}'), HttpMethod::GET, '/users/{id}', ['id'], false],
            [new HEAD('/users/{id}'), HttpMethod::HEAD, '/users/{id}', ['id'], false],
            [new HTTP(HttpMethod::GET, '/users/{id}/groups/{groupId}', true), HttpMethod::GET, '/users/{id}/groups/{groupId}', ['id', 'groupId'], true],
            [new OPTIONS('/users/{id}'), HttpMethod::OPTIONS, '/users/{id}', ['id'], false],
            [new PATCH('/users/{id}'), HttpMethod::PATCH, '/users/{id}', ['id'], true],
            [new POST('/users/{id}'), HttpMethod::POST, '/users/{id}', ['id'], true],
            [new PUT('/users/{id}'), HttpMethod::PUT, '/users/{id}', ['id'], true],
        ];
    }
}
