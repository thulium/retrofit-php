<?php
declare(strict_types=1);

namespace Retrofit;

use Psr\Http\Message\ResponseInterface;

/**
 * Create an error response from {@code rawResponse} with {@code body} as the error body.
 */
interface Response
{
    /**
     * The raw response from the HTTP client.
     */
    public function raw(): ResponseInterface;

    /**
     * HTTP status code.
     */
    public function code(): int;

    /**
     * HTTP status message or null if unknown.
     */
    public function message(): ?string;

    /**
     * HTTP headers.
     */
    public function headers(): array;

    /**
     * Returns true if {@link Response::code() code()} is in the range [200..300).
     */
    public function isSuccessful(): bool;

    /**
     * The deserialized response body of a {@link Response::isSuccessful() successful} response.
     */
    public function body(): mixed;

    /**
     * The raw response body of an {@link Response::isSuccessful() unsuccessful} response.
     */
    public function errorBody(): ResponseInterface;
}
