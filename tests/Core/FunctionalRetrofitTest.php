<?php

declare(strict_types=1);

namespace Retrofit\Tests\Core;

use donatj\MockWebServer\MockWebServer;
use donatj\MockWebServer\Response;
use GuzzleHttp\Client;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Retrofit\Client\Guzzle7\Guzzle7HttpClient;
use Retrofit\Converter\SymfonySerializer\SymfonySerializerConverterFactory;
use Retrofit\Core\Retrofit;
use Retrofit\Tests\Fixtures\Api\FullyValidApi;
use Symfony\Component\Serializer\Serializer;

class FunctionalRetrofitTest extends TestCase
{
    private MockWebServer $mockWebServer;

    private FullyValidApi $fullyValidApi;

    protected function setUp(): void
    {
        parent::setUp();

//        $this->mockWebServer = new MockWebServer();
//        $this->mockWebServer->start();

//        $this->fullyValidApi = $this->createFullyValidApiMock();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->mockWebServer->stop();
    }

    #[Test]
    public function shouldGenerateProperResponse(): void
    {
        // given
        $this->mockWebServer = new MockWebServer();
        $this->mockWebServer->start();
        $url = $this->mockWebServer->setResponseOfPath('/info/sample', new Response('foo bar content'));

        $retrofit = Retrofit::Builder()
            ->baseUrl($url)
            ->client(new Guzzle7HttpClient(new Client()))
            ->addConverterFactory(new SymfonySerializerConverterFactory(new Serializer()))
            ->build();

        /** @var FullyValidApi $service */
        $this->fullyValidApi = $retrofit->create(FullyValidApi::class);

        $content = file_get_contents($url);

//        $this->fullyValidApi = $this->createFullyValidApiMock();

        $call = $this->fullyValidApi->getInfo('sample-user');

        // when
        $result = $call->request();

        // then
        $body = $result->getBody();
        $this->assertEquals('foo bar content', $body->getContents());
    }

    private function createFullyValidApiMock(): FullyValidApi
    {
        $retrofit = Retrofit::Builder()
            ->baseUrl($this->mockWebServer->getServerRoot())
            ->client(new Guzzle7HttpClient(new Client()))
            ->addConverterFactory(new SymfonySerializerConverterFactory(new Serializer()))
            ->build();

        /** @var FullyValidApi $service */
        return $retrofit->create(FullyValidApi::class);
    }
}
