<?php

declare(strict_types=1);

namespace Termyn\SharedKernel\Domain\Currency\Unit;

use Webmozart\Assert\Assert;

final class Subunit
{
    public function __construct(
        public readonly string $code,
        public readonly string $symbol,
        public readonly int $fraction,
    ) {
        Assert::notEmpty($this->code);
        Assert::notEmpty($this->symbol);
        Assert::oneOf($this->fraction, [1, 10, 100, 1000]);
    }

    public function equals(self $that): bool
    {
        return $that->code === $this->code
            && $that->symbol === $this->symbol
            && $that->fraction === $this->fraction;
    }

    public function precision(): int
    {
        return intval(log10($this->fraction));
    }
}
