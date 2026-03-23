<?php

declare(strict_types=1);

namespace EstissySystems\PhpCommon\ValueObject;

use Ds\Hashable;

readonly class Pagination implements Hashable
{
    private string $hash;

    private function __construct(private Limit $limit, private Offset $offset)
    {
        $this->hash = hash('sha256', $limit->hash() . $offset->hash());
    }

    public static function fromLimitAndOffset(Limit $limit, Offset $offset): self
    {
        return new self($limit, $offset);
    }

    public static function fromPageAndPageSize(int $page, int $pageSize): self
    {
        if ($page < 1) {
            throw new \LogicException('Page must be a positive integer, got ' . $page);
        }

        $limit = Limit::fromInteger($pageSize);
        $offset = Offset::fromInteger(($page - 1) * $pageSize);

        return new self($limit, $offset);
    }

    public function limit(): Limit
    {
        return $this->limit;
    }

    public function offset(): Offset
    {
        return $this->offset;
    }

    public function equals(mixed $obj): bool
    {
        return $obj instanceof self
            && $this->limit->equals($obj->limit)
            && $this->offset->equals($obj->offset);
    }

    public function hash(): string
    {
        return $this->hash;
    }
}
