<?php

declare(strict_types=1);

namespace Retrofit\Core\Attribute\Response;

use InvalidArgumentException;

/**
 * Convenient class for handle types in {@link ResponseBody} and {@link ErrorBody} attributes.
 *
 * @internal
 */
trait WithBody
{
    public function __construct(
        private readonly string $rawType,
        private readonly ?string $parametrizedType = null,
    )
    {
        if (!is_null($this->parametrizedType) && $this->rawType !== 'array') {
            throw new InvalidArgumentException('Parametrized type can be set only for array raw type.');
        }
    }

    public function rawType(): string
    {
        return $this->rawType;
    }

    public function parametrizedType(): ?string
    {
        return $this->parametrizedType;
    }
}
