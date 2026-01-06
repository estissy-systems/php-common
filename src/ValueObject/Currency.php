<?php

declare(strict_types=1);

namespace EstissySystems\PhpCommon\ValueObject;

/**
 * Three letter currency codes from ISO 4217
 */
enum Currency: string
{
    case USD = 'USD';
    case EUR = 'EUR';
    case GBP = 'GBP';
    case JPY = 'JPY';
    case CNY = 'CNY';
    case CHF = 'CHF';
    case CAD = 'CAD';
    case AUD = 'AUD';
    case NZD = 'NZD';
    case SEK = 'SEK';
    case NOK = 'NOK';
    case DKK = 'DKK';
    case PLN = 'PLN';
    case CZK = 'CZK';
    case HUF = 'HUF';
    case RON = 'RON';
    case BGN = 'BGN';
    case HRK = 'HRK';
    case INR = 'INR';
    case BRL = 'BRL';
    case MXN = 'MXN';
    case KRW = 'KRW';
    case SGD = 'SGD';
    case HKD = 'HKD';
    case ZAR = 'ZAR';

    public function getDecimals(): int
    {
        return match ($this) {
            Currency::USD => 2,
            Currency::EUR => 2,
            Currency::GBP => 2,
            Currency::JPY => 0,
            Currency::CNY => 2,
            Currency::CHF => 2,
            Currency::CAD => 2,
            Currency::AUD => 2,
            Currency::NZD => 2,
            Currency::SEK => 2,
            Currency::NOK => 2,
            Currency::DKK => 2,
            Currency::PLN => 2,
            Currency::CZK => 2,
            Currency::HUF => 2,
            Currency::RON => 2,
            Currency::BGN => 2,
            Currency::HRK => 2,
            Currency::INR => 2,
            Currency::BRL => 2,
            Currency::MXN => 2,
            Currency::KRW => 0,
            Currency::SGD => 2,
            Currency::HKD => 2,
            Currency::ZAR => 2,
        };
    }
}
