<?php
declare(strict_types=1);

namespace Retrofit;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface HttpClient
{
    public function send(RequestInterface $request): ResponseInterface;

    public function sendAsync(RequestInterface $request, callable $onResponse, callable $onFailure): void;

    public function wait(): void;
}
