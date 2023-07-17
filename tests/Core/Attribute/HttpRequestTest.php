<?php
declare(strict_types=1);

namespace Retrofit\Tests\Core\Attribute;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Retrofit\Core\Attribute\DELETE;
use Retrofit\Core\Attribute\GET;
use Retrofit\Core\Attribute\HEAD;
use Retrofit\Core\Attribute\HTTP;
use Retrofit\Core\Attribute\HttpRequest;
use Retrofit\Core\Attribute\OPTIONS;
use Retrofit\Core\Attribute\PATCH;
use Retrofit\Core\Attribute\POST;
use Retrofit\Core\Attribute\PUT;
use Retrofit\Core\HttpMethod;

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
