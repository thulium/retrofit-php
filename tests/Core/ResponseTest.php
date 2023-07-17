<?php
declare(strict_types=1);

namespace Retrofit\Tests\Core;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use Retrofit\Core\Response;
use stdClass;

class ResponseTest extends TestCase
{
    #[Test]
    public function shouldGetRawResponse(): void
    {
        //given
        $responseInterface = new \GuzzleHttp\Psr7\Response();
        $response = new Response($responseInterface, null, null);

        //when
        $actualRawResponse = $response->raw();

        //then
        $this->assertSame($responseInterface, $actualRawResponse);
    }

    #[Test]
    public function shouldGetCode(): void
    {
        //given
        $responseInterface = new \GuzzleHttp\Psr7\Response(401);
        $response = new Response($responseInterface, null, null);

        //when
        $code = $response->code();

        //then
        $this->assertSame(401, $code);
    }

    #[Test]
    public function shouldGetMessage(): void
    {
        //given
        $responseInterface = new \GuzzleHttp\Psr7\Response(401);
        $response = new Response($responseInterface, null, null);

        //when
        $message = $response->message();

        //then
        $this->assertSame('Unauthorized', $message);
    }

    #[Test]
    public function shouldGetHeaders(): void
    {
        //given
        $responseInterface = new \GuzzleHttp\Psr7\Response(401, ['Accept' => 'application/json']);
        $response = new Response($responseInterface, null, null);

        //when
        $headers = $response->headers();

        //then
        $this->assertSame(['Accept' => ['application/json']], $headers);
    }

    #[Test]
    #[TestWith([199, false])]
    #[TestWith([200, true])]
    #[TestWith([204, true])]
    #[TestWith([299, true])]
    #[TestWith([300, false])]
    #[TestWith([400, false])]
    #[TestWith([500, false])]
    public function shouldCheckIsResponseSuccessful(int $status, bool $expectedSuccessful): void
    {
        //given
        $responseInterface = new \GuzzleHttp\Psr7\Response($status);
        $response = new Response($responseInterface, null, null);

        //when
        $successful = $response->isSuccessful();

        //then
        $this->assertSame($expectedSuccessful, $successful);
    }

    #[Test]
    public function shouldGetBody(): void
    {
        //given
        $responseInterface = new \GuzzleHttp\Psr7\Response(200);
        $body = new stdClass();
        $body->field = 'value';
        $response = new Response($responseInterface, $body, null);

        //when
        $actualBody = $response->body();

        //then
        $this->assertSame($body, $actualBody);
    }

    #[Test]
    public function shouldGetErrorBody(): void
    {
        //given
        $responseInterface = new \GuzzleHttp\Psr7\Response(200);
        $errorBody = new stdClass();
        $errorBody->success = false;
        $response = new Response($responseInterface, null, $errorBody);

        //when
        $actualErrorBody = $response->errorBody();

        //then
        $this->assertSame($errorBody, $actualErrorBody);
    }

    #[Test]
    public function shouldAllowEmptyBodyAndEmptyErrorBody(): void
    {
        //given
        $responseInterface = new \GuzzleHttp\Psr7\Response(200);
        $response = new Response($responseInterface, null, null);

        //when
        $body = $response->body();
        $errorBody = $response->errorBody();

        //then
        $this->assertNull($body);
        $this->assertNull($errorBody);
    }
}
