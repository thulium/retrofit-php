<?php
declare(strict_types=1);

namespace Retrofit\Internal;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Retrofit\Call;
use Retrofit\Callback;
use Retrofit\Converter\ResponseBodyConverter;
use Retrofit\HttpClient;
use Retrofit\Response;
use RuntimeException;
use Throwable;

class HttpClientCall implements Call
{
    private $requests;

    public function __construct(
        private readonly HttpClient $httpClient,
        private readonly RequestInterface $request,
        private readonly ResponseBodyConverter $responseBodyConverter,
        private readonly ?ResponseBodyConverter $errorBodyConverter
    )
    {
    }

    public function execute(): Response
    {
        $response = $this->httpClient->send($this->request());
        return $this->createResponse($response);
    }

    public function enqueue(Callback $callback): Call
    {
        $this->httpClient->sendAsync($this->request(), $callback->onResponse(), $callback->onFailure());
        return $this;
    }

    public function wait(): void
    {
        $this->httpClient->wait();
    }

    public function request(): RequestInterface
    {
        return $this->request;
    }

    private function createResponse(ResponseInterface $response): Response
    {
        $code = $response->getStatusCode();
        if ($code >= 200 && $code < 300) {
            try {
                $responseBody = $this->responseBodyConverter->convert($response->getBody());
                return new Response($response, $responseBody, null);
            } catch (Throwable $throwable) {
                throw new RuntimeException('Retrofit: Could not convert response body', 0, $throwable);
            }
        }

        if (is_null($this->errorBodyConverter)) {
            return new Response($response, null, null);
        }

        try {
            $errorBody = $this->errorBodyConverter->convert($response->getBody());
            return new Response($response, null, $errorBody);
        } catch (Throwable $throwable) {
            throw new RuntimeException('Retrofit: Could not convert error body', 0, $throwable);
        }
    }
}
