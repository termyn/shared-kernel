<?php

declare(strict_types=1);

namespace Termyn\SharedKernel\Domain\Time\Period;

use Termyn\SharedKernel\Domain\Time;
use Termyn\SharedKernel\Domain\Time\TimePeriod;

final class Seconds extends TimePeriod
{
    public static function fromDays(Days $days): self
    {
        return new self($days->value * Time::SECONDS_IN_DAY);
    }

    public static function fromHours(Hours $hours): self
    {
        return new self($hours->value * Time::SECONDS_IN_HOUR);
    }

    public static function fromMinutes(Minutes $minutes): self
    {
        return new self($minutes->value * Time::SECONDS_IN_MINUTE);
    }

    public function increase(self $seconds): self
    {
        return new self($this->value + $seconds->value);
    }

    public function decrease(self $seconds): self
    {
        return new self($this->value - $seconds->value);
    }
}
