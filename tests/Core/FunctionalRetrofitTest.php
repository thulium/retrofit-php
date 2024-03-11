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
use Retrofit\Tests\Fixtures\Model\UserRequest;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\JsonSerializableNormalizer;
use Symfony\Component\Serializer\Serializer;

class FunctionalRetrofitTest extends TestCase
{
    private MockWebServer $mockWebServer;

    private FullyValidApi $fullyValidApi;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockWebServer = new MockWebServer();
        $this->mockWebServer->start();

        $this->fullyValidApi = $this->createFullyValidApiMock();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->mockWebServer->stop();
    }

    #[Test]
    public function shouldGenerateProperResponseOnGetCall(): void
    {
        // given
        $this->mockWebServer->setResponseOfPath('/info/sample-user', new Response('foo bar content'));

        $call = $this->fullyValidApi->getInfo('sample-user');

        // when
        $result = $call->execute();

        // then
        $this->assertEquals(200, $result->code());
        $this->assertEquals('foo bar content', $result->body());
    }

    #[Test]
    public function shouldGenerateProperResponseOnPostCall(): void
    {
        // given
        $this->mockWebServer->setResponseOfPath('/users/sample-user', new Response('foo bar content'));

        $userRequest = (new UserRequest())
            ->setId(1)
            ->setLogin('sample-user');

        $call = $this->fullyValidApi->createUser('sample-user', $userRequest);

        // when
        $result = $call->execute();

        // then
        $this->assertEquals(200, $result->code());
        $this->assertEquals('foo bar content', $result->body());
    }

    #[Test]
    public function shouldHandleRequestOnPostCall(): void
    {
        // given
        $this->mockWebServer->setResponseOfPath('/users/sample-user', new Response('foo bar content'));

        $userRequest = (new UserRequest())
            ->setId(1)
            ->setLogin('sample-user');

        $call = $this->fullyValidApi->createUser('sample-user', $userRequest);

        // when
        $call->execute();

        // then
        $input = $this->mockWebServer->getLastRequest()->getInput();
        $this->assertJson($input);
        $this->assertEquals('{"id":1,"login":"sample-user"}', $input);
    }

    #[Test]
    public function shouldProcessUrlBeforePath(): void
    {
        // given
        $this->mockWebServer->setResponseOfPath('/users/jon', new Response('foo bar content'));

        $call = $this->fullyValidApi->pathIsBeforeUrl('jon', 'https://thulium.pl/users/{login}');

        // when
        $call->execute();

        // then
        $this->assertSame('/users/jon', $call->request()->getUri()->getPath());
    }

    #[Test]
    public function shouldAddQueryStringToUrlAttribute(): void
    {
        // given
        $call = $this->fullyValidApi->urlWithQuery('new', 'https://thulium.pl/users');

        // when
        $call->execute();

        // then
        $this->assertSame('group=new', $call->request()->getUri()->getQuery());
    }

    private function createFullyValidApiMock(): FullyValidApi
    {
        $normalizers = [new JsonSerializableNormalizer()];
        $encoders = [new JsonEncoder()];
        $serializer = new Serializer($normalizers, $encoders);
        $retrofit = Retrofit::Builder()
            ->baseUrl($this->mockWebServer->getServerRoot())
            ->client(new Guzzle7HttpClient(new Client()))
            ->addConverterFactory(new SymfonySerializerConverterFactory($serializer))
            ->build();

        /** @var FullyValidApi $service */
        return $retrofit->create(FullyValidApi::class);
    }
}
