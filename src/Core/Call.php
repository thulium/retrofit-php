<?php

declare(strict_types=1);

namespace Retrofit\Core;

use Psr\Http\Message\RequestInterface;

/**
 * An invocation of a Retrofit method that sends a request to a webserver and returns a response. Each call yields its
 * own HTTP request and response pair.
 *
 * Calls may be executed synchronously with {@link Call::execute() execute}, or asynchronously with
 * {@link Call::enqueue() enqueue}.
 *
 * @api
 */
interface Call
{
    /**
     * Synchronously send the request and return its response.
     */
    public function execute(): Response;

    /**
     * Enqueue the request and notify {@link Callback} of its response or if an error occurred talking to the server,
     * creating the request, or processing the response.
     */
    public function enqueue(Callback $callback): Call;

    /**
     * Asynchronously send the enqueued requests.
     */
    public function wait(): void;

    /**
     * The original HTTP request.
     */
    public function request(): RequestInterface;
}
