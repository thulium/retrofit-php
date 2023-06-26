<?php
declare(strict_types=1);

namespace Retrofit;

use Psr\Http\Message\RequestInterface;

interface Call
{
    public function execute(): Response;

    public function enqueue(Callback $callback): Call;

    public function wait(): void;

    public function request(): RequestInterface;
}
