<?php

declare(strict_types=1);

namespace EstissySystems\PhpCommon\ValueObject;

use Ds\Hashable;
use LogicException;
use NumberFormatter;
use RoundingMode;

/**
 * Represents money, negative values available. The amount is stored in the smallest unit as an integer.
 */
readonly class Money implements Hashable
{
    private int $amount;
    private Currency $currency;
    private string $hash;

    private function __construct(int $amount, Currency $currency)
    {
        $this->amount = $amount;
        $this->currency = $currency;
        $this->hash = hash('sha256', self::class . $amount . $currency->value);
    }

    /**
     * @param int $amount The amount in the smallest unit as an integer
     */
    public static function fromAmountAndCurrency(int $amount, Currency $currency): Money
    {
        return new Money($amount, $currency);
    }

    public function toString(): string
    {
        $divisor = 10 ** $this->currency()->getDecimals();
        $dividedAmount = $this->amount / $divisor;

        return sprintf("%.{$this->currency()->getDecimals()}f %s", $dividedAmount, $this->currency()->value);
    }

    public function currency(): Currency
    {
        return $this->currency;
    }

    public function toHumanString(string $humanLocale): string
    {
        $divisor = 10 ** $this->currency()->getDecimals();
        $dividedAmount = $this->amount / $divisor;

        $numberFormatter = new NumberFormatter($humanLocale, NumberFormatter::CURRENCY);

        $result = $numberFormatter->formatCurrency($dividedAmount, $this->currency()->value);

        if ($result === false) {
            throw new LogicException(
                "Error while formatting amount $dividedAmount in currency {$this->currency()->value} and locale $humanLocale."
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

    public function add(Money $money): Money
    {
        if ($this->currency() !== $money->currency()) {
            throw new LogicException(
                "Error while adding money. Money currency {$this->currency()->value} is not equal added money currency {$money->currency()->value}."
            );
        }

        return new Money($this->amount() + $money->amount(), $this->currency());
    }

    public function amount(): int
    {
        return $this->amount;
    }

    public function subtract(Money $money): Money
    {
        if ($this->currency() !== $money->currency()) {
            throw new LogicException(
                "Error while subtracting money. Money currency {$this->currency()->value} is not equal subtracted money currency {$money->currency()->value}."
            );
        }

        return new Money($this->amount() - $money->amount(), $this->currency());
    }

    public function multiply(float $multiplier, RoundingMode $roundingMode = RoundingMode::HalfAwayFromZero): Money
    {
        $newAmount = (int)round($this->amount() * $multiplier, 0, $roundingMode);

        return new Money($newAmount, $this->currency());
    }

    public function divide(float $divisor, RoundingMode $roundingMode = RoundingMode::HalfAwayFromZero): Money
    {
        $newAmount = (int)round($this->amount() / $divisor, 0, $roundingMode);

        return new Money($newAmount, $this->currency());
    }

    public function convert(
        Currency $targetCurrency,
        float $conversionRate,
        RoundingMode $roundingMode = RoundingMode::HalfAwayFromZero
    ): Money {
        $newAmount = (int)round($this->amount() * $conversionRate, 0, $roundingMode);

        return new Money($newAmount, $targetCurrency);
    }
}
