<?php
declare(strict_types=1);

namespace Retrofit\Internal;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Retrofit\Call;
use Retrofit\Callback;
use Retrofit\HttpClient;
use Retrofit\Response;
use Throwable;

class HttpClientCall implements Call
{
    private $requests;

    public function __construct(
        private readonly HttpClient $httpClient,
        private readonly RequestInterface $request
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
        return $this->httpClient->sendAsync($this->request(), $callback->onResponse(), $callback->onFailure());
    }

    public function wait(): void
    {
        $this->httpClient->wait();
    }

    public function request(): RequestInterface
    {
        return $this->request;
    }

    private function createResponse(ResponseInterface $response): RetrofitResponse
    {
        $code = $response->getStatusCode();
        if ($code >= 200 && $code < 300) {
            try {
                $responseBody = $this->serviceMethod->toResponseBody($response);
            } catch (Throwable $throwable) {
                throw new ResponseHandlingFailedException(
                    $this->request(),
                    $response,
                    'Retrofit: Could not convert response body',
                    $throwable
                );
            }

            return new RetrofitResponse($response, $responseBody, null);
        }

        try {
            $errorBody = $this->serviceMethod->toErrorBody($response);
        } catch (Throwable $throwable) {
            throw new ResponseHandlingFailedException(
                $this->request(),
                $response,
                'Retrofit: Could not convert error body',
                $throwable
            );
        }

        return new RetrofitResponse($response, null, $errorBody);
    }
}
