<?php

declare(strict_types=1);

namespace Termyn\SharedKernel\Domain;

final class Money
{
    final public const ROUND_HALF_EVEN = PHP_ROUND_HALF_EVEN;
    final public const ROUND_HALF_DOWN = PHP_ROUND_HALF_DOWN;
    final public const ROUND_HALF_ODD = PHP_ROUND_HALF_ODD;
    final public const ROUND_HALF_UP = PHP_ROUND_HALF_UP;

    public readonly float $amount;
    private readonly int $amountInSubunit;

    final protected function __construct(
        int|float $amount,
        public readonly Currency $currency,
    ) {
        $fraction = $this->currency->fraction();

        $this->amountInSubunit = intval($amount * $fraction);
        $this->amount = floatval($this->amountInSubunit / $fraction);
    }

    public static function of(
        int|float $amount,
        Currency $currency,
    ): self {
        return new self($amount, $currency);
    }

    public static function ofSub(
        int $amount,
        Currency $currency,
    ): self {
        return new self($amount / $currency->fraction(), $currency);
    }

    public function comparable(self $that): bool
    {
        return $this->currency->equals($that->currency);
    }

    public function equals(self $that): bool
    {
        return $this->comparable($that)
            && $this->amountInSubunit === $that->amountInSubunit;
    }

    public function greaterThan(self $that): bool
    {
        return $this->compare($that) > 0;
    }

    public function greaterThanOrEqualTo(self $that): bool
    {
        return $this->compare($that) >= 0;
    }

    public function lessThan(self $that): bool
    {
        return $this->compare($that) < 0;
    }

    public function lessThanOrEqualTo(self $that): bool
    {
        return $this->compare($that) <= 0;
    }

    public function equalToZero(): bool
    {
        return $this->amountInSubunit === 0;
    }

    public function moreThanZero(): bool
    {
        return $this->amountInSubunit > 0;
    }

    public function lessThanZero(): bool
    {
        return $this->amountInSubunit < 0;
    }

    public function add(self $that): self
    {
        if (! $this->comparable($that)) {
            throw new MismatchCurrencies($this, $that);
        }

        return self::ofSub(
            amount: $this->amountInSubunit + $that->amountInSubunit,
            currency: $this->currency,
        );
    }

    public function subtract(self $that): self
    {
        if (! $this->comparable($that)) {
            throw new MismatchCurrencies($this, $that);
        }

        return self::ofSub(
            amount: $this->amountInSubunit - $that->amountInSubunit,
            currency: $this->currency
        );
    }

    /**
     * @phpstan-param self::ROUND_* $rounding
     */
    public function multiply(
        int|float $factor,
        int $rounding = self::ROUND_HALF_UP,
    ): self {
        $result = round(
            num: $this->amountInSubunit * $factor,
            mode: $rounding,
        );

        return self::ofSub(
            amount: intval($result),
            currency: $this->currency,
        );
    }

    /**
     * @phpstan-param self::ROUND_* $rounding
     */
    public function divide(
        int|float $divisor,
        int $rounding = self::ROUND_HALF_UP,
    ): self {
        $result = round(
            num: $this->amountInSubunit / $divisor,
            mode: $rounding,
        );

        return self::ofSub(
            amount: intval($result),
            currency: $this->currency,
        );
    }

    public function changeToPositive(): self
    {
        return self::ofSub(
            amount: abs($this->amountInSubunit),
            currency: $this->currency,
        );
    }

    public function changeToNegative(): self
    {
        return self::ofSub(
            amount: (-1 * $this->amountInSubunit),
            currency: $this->currency,
        );
    }

    private function compare(self $that): int
    {
        if (! $this->comparable($that)) {
            throw new MismatchCurrencies($this, $that);
        }

        return $this->amountInSubunit <=> $that->amountInSubunit;
    }
}
