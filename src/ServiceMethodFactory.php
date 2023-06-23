<?php
declare(strict_types=1);

namespace Retrofit;

class ServiceMethodFactory
{
    public static function create(HttpClient $httpClient, string $service, string $method): ServiceMethod
    {
        return new class($service, $method) implements ServiceMethod {
            public function __construct(
                private readonly string $service,
                private readonly string $method,
            )
            {
            }

            public function invoke(array $args): Call
            {
                return new class($this->service, $this->method) implements Call {
                    public function __construct(
                        private readonly string $service,
                        private readonly string $method,
                    )
                    {
                    }

                    public function execute(): void
                    {
                        echo "{$this->service}::{$this->method}";
                    }
                };
            }
        };
    }
}
