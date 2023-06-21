<?php
declare(strict_types=1);

namespace Retrofit;

use InvalidArgumentException;

class Retrofit
{
    /**
     * @param string $service
     * @return object
     * @throws InvalidArgumentException
     */
    public function create(string $service): object
    {
        $this->validateServiceInterface($service);
        return new \stdClass();
    }

    private function validateServiceInterface(string $service): void
    {
        if (!interface_exists($service)) {
            throw new InvalidArgumentException('API declarations must be interface');
        }
    }
}
