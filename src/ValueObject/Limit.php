<?php

declare(strict_types=1);

namespace EstissySystems\PhpCommon\ValueObject;

use Ds\Hashable;
use LogicException;

readonly class Limit implements Hashable
{
    private string $hash;

    private function __construct(private int $value)
    {
        if ($value < 1) {
            throw new LogicException('Limit must be a positive integer, got ' . $value);
        }

        $this->hash = hash('sha256', (string) $value);
    }

    public static function fromInteger(int $value): self
    {
        return new self($value);
    }

    public function toInteger(): int
    {
        return $this->value;
    }

    public function equals(mixed $obj): bool
    {
        return $obj instanceof self && $obj->value === $this->value;
    }

    public function hash(): string
    {
        return $this->hash;
    }
}
