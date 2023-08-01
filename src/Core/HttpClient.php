<?php

declare(strict_types=1);

namespace Retrofit\Core;

use Closure;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * {@link https://www.php-fig.org/psr/psr-7/ PSR-7} compliant http client interface.
 *
 * @api
 */
interface HttpClient
{
    /**
     * Sends a PSR-7 request and return PSR-7 response.
     */
    public function send(RequestInterface $request): ResponseInterface;

    /**
     * Enqueues a PSR-7 async request.
     *
     * Both <code>onResponse</code> and <code>onFailure</code> pass a {@link Call} object as a first parameter.
     * Second parameter depends on callback {@see Response} and {@see \Throwable} respectively.
     */
    public function sendAsync(RequestInterface $request, Closure $onResponse, Closure $onFailure): void;

    /**
     * Calling this method should execute any enqueues request.
     */
    public function wait(): void;
}
