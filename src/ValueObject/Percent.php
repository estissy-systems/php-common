<?php

declare(strict_types=1);

namespace EstissySystems\PhpCommon\ValueObject;

use Ds\Hashable;
use LogicException;
use NumberFormatter;
use RoundingMode;

class Percent implements Hashable
{
    private int $basisPoints;
    private string $hash;

    private function __construct(int $basisPoints)
    {
        $this->basisPoints = $basisPoints;
        $this->hash = hash('sha256', self::class . $this->basisPoints);
    }

    public static function zero(): Percent
    {
        return new Percent(0);
    }

    public static function hundred(): Percent
    {
        return new Percent(10000);
    }

    public static function fromFloat(
        float $percent,
        RoundingMode $roundingMode = RoundingMode::HalfAwayFromZero
    ): Percent {
        $basisPoints = (int)round($percent * 100, 0, $roundingMode);

        return new Percent($basisPoints);
    }

    public function add(Percent $percent): Percent
    {
        return Percent::fromBasisPoints($this->basisPoints() + $percent->basisPoints());
    }

    public static function fromBasisPoints(int $basisPoints): Percent
    {
        return new Percent($basisPoints);
    }

    public function basisPoints(): int
    {
        return $this->basisPoints;
    }

    public function subtract(Percent $percent): Percent
    {
        return Percent::fromBasisPoints($this->basisPoints() - $percent->basisPoints());
    }

    public function increaseBy(Percent $percent, RoundingMode $roundingMode = RoundingMode::HalfAwayFromZero): Percent
    {
        $basisPoints = (int)round(($this->basisPoints() * (10000 + $percent->basisPoints())) / 10000, 0, $roundingMode);

        return Percent::fromBasisPoints($basisPoints);
    }

    public function toString(): string
    {
        return $this->basisPoints / 100 . '%';
    }

    public function toHumanString(
        string $humanLocale,
        int $minNumberOfFractionDigits = 2,
        int $maxNumberOfFractionDigits = 2
    ): string {
        $numberFormatter = new NumberFormatter($humanLocale, NumberFormatter::PERCENT);
        $numberFormatter->setAttribute(NumberFormatter::MIN_FRACTION_DIGITS, $minNumberOfFractionDigits);
        $numberFormatter->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, $maxNumberOfFractionDigits);

        $result = $numberFormatter->format($this->basisPoints / 10000);

        if ($result === false) {
            throw new LogicException(
                "Error while formatting percent basis points {$this->basisPoints} for locale $humanLocale, minimum number of fraction digits $minNumberOfFractionDigits and maximum number of fraction digits $maxNumberOfFractionDigits."
            );
        }

        return $result;
    }

    public function equals($obj): bool
    {
        if (($obj instanceof Hashable) === false) {
            return false;
        }

        return $this->hash === $obj->hash();
    }

    public function hash(): string
    {
        return $this->hash;
    }
}
