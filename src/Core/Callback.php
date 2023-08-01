<?php

declare(strict_types=1);

namespace Retrofit\Core;

use Throwable;

/**
 * Communicates responses from a server.
 *
 * @api
 */
interface Callback
{
    /**
     * Invoked for receive HTTP response.
     *
     * Note: An HTTP response may still indicate an application-level failure such as a 400 or 500.
     * Call {@link Response::isSuccessful()} to determine if the response indicates success.
     */
    public function onResponse(Call $call, Response $response): void;

    /**
     * Invoked when a network exception occurred talking to the server or when an unexpected exception occurred creating
     * the request or processing the response.
     */
    public function onFailure(Call $call, Throwable $t): void;
}
