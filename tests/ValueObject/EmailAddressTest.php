<?php

declare(strict_types=1);

namespace ValueObject;

use EstissySystems\PhpCommon\ValueObject\EmailAddress;
use LogicException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class EmailAddressTest extends TestCase
{
    /**
     * @return array<int, array<int, string>>
     */
    public static function getEmailAddressWithAllowedSpecialCharacters(): array
    {
        return [
            ['a!b@gmail.com'],
            ['a#b@gmail.com'],
            ['a$b@gmail.com'],
            ['a%b@gmail.com'],
            ['a&b@gmail.com'],
            ["a'b@gmail.com"],
            ['a*b@gmail.com'],
            ['a+b@gmail.com'],
            ['a-b@gmail.com'],
            ['a/b@gmail.com'],
            ['a=b@gmail.com'],
            ['a?b@gmail.com'],
            ['a^b@gmail.com'],
            ['a_b@gmail.com'],
            ['a`b@gmail.com'],
            ['a{b@gmail.com'],
            ['a|b@gmail.com'],
            ['a}b@gmail.com'],
            ['a~b@gmail.com'],
            ['a.b@gmail.com'],
        ];
    }

    public function testIsValidShouldReturnFalseWhenEmptyEmailProvided(): void
    {
        self::assertFalse(EmailAddress::isValid(''));
    }

    public function testIsValidShouldReturnFalseWhenEmailWith65CharacterLocalPartProvided(): void
    {
        self::assertFalse(
            EmailAddress::isValid(
                'ODlllBqC1dZy021xeZJgkzSbvTKqe9Nbjw2allNGkXe4iYObVtyulxUOrNOJGJvVE@gmail.com'
            )
        );
    }

    public function testIsValidShouldReturnTrueWhenEmailWith64CharacterLocalPartProvided(): void
    {
        self::assertTrue(
            EmailAddress::isValid(
                'ODlllBqC1dZy021xeZJgkzSbvTKqe9Nbjw2allNGkXe4iYObVtyulxUOrNOJGJvV@gmail.com'
            )
        );
    }

    public function testIsValidShouldReturnFalseWhenEmailWith64CharacterDomainPartProvided(): void
    {
        self::assertFalse(
            EmailAddress::isValid(
                'x@ZYVT0hMU6tZBFPqNYVFXKZ4gW6a0fG50BCaLPPWrd73x6acgXXrllvwXLMxlhGux.9zupuQUaUTLZzAR3Xd8x7wDxoy1Nc4cGOtFItBOXIJmD3IOZKUu8ziJN6IJhf7n.9zupuQUaUTLZzAR3Xd8x7wDxoy1Nc4cGOtFItBOXIJmD3IOZKUu8ziJN6IJhf7n.com'
            )
        );
    }

    public function testIsValidShouldReturnTrueWhenEmailWith63CharacterDomainPartProvided(): void
    {
        self::assertTrue(
            EmailAddress::isValid(
                'x@ZYVT0hMU6tZBFPqNYVFXKZ4gW6a0fG50BCaLPPWrd73x6acgXXrllvwXLMxlhGu.9zupuQUaUTLZzAR3Xd8x7wDxoy1Nc4cGOtFItBOXIJmD3IOZKUu8ziJN6IJhf7n.9zupuQUaUTLZzAR3Xd8x7wDxoy1Nc4cGOtFItBOXIJmD3IOZKUu8ziJN6IJhf7n.com'
            )
        );
    }

    public function testIsValidShouldReturnFalseWhenEmailWith255CharactersProvided(): void
    {
        self::assertFalse(
            EmailAddress::isValid(
                'OND1el9bTqB2eWafuYfCWRdnzBr9HI2hhJU3GaAAWOlqZ5mIhoBt7y5y570G2qQ6@aYrN1DvPV5KXlczEsNCGOAAJ6HOcdoCnBJlnPalyDYMOOpJBAidyImSS3L.9zupuQUaUTLZzAR3Xd8x7wDxoy1Nc4cGOtFItBOXIJmD3IOZKUu8ziJN6IJhf7n.9zupuQUaUTLZzAR3Xd8x7wDxoy1Nc4cGOtFItBOXIJmD3IOZKUu8ziJN6IJhf7n.com'
            )
        );
    }

    public function testIsValidShouldReturnTrueWhenEmailWith254CharactersProvided(): void
    {
        self::assertTrue(
            EmailAddress::isValid(
                'OND1el9bTqB2eWafuYfCWRdnzBr9HI2hhJU3GaAAWOlqZ5mIhoBt7y5y570G2qQ6@YrN1DvPV5KXlczEsNCGOAAJ6HOcdoCnBJlnPalyDYMOOpJBAidyImSS3L.9zupuQUaUTLZzAR3Xd8x7wDxoy1Nc4cGOtFItBOXIJmD3IOZKUu8ziJN6IJhf7n.9zupuQUaUTLZzAR3Xd8x7wDxoy1Nc4cGOtFItBOXIJmD3IOZKUu8ziJN6IJhf7n.com'
            )
        );
    }

    public function testIsValidShouldReturnFalseWhenEmailWithDotAtTheBeginningOfLocalPartProvided(): void
    {
        self::assertFalse(
            EmailAddress::isValid(
                '.a@gmail.com'
            )
        );
    }

    public function testIsValidShouldReturnFalseWhenEmailWithDotAtTheEndOfLocalPartProvided(): void
    {
        self::assertFalse(
            EmailAddress::isValid(
                'a.@gmail.com'
            )
        );
    }

    public function testIsValidShouldReturnFalseWhenEmailWithDotAtTheBeginningOfDomainPartProvided(): void
    {
        self::assertFalse(
            EmailAddress::isValid(
                'a@.gmail.com'
            )
        );
    }

    public function testIsValidShouldReturnFalseWhenEmailWithDotAtTheEndOfDomainPartProvided(): void
    {
        self::assertFalse(
            EmailAddress::isValid(
                'a@gmail.com.'
            )
        );
    }

    public function testIsValidShouldReturnFalseWhenEmailWithTwoDotsInLocalPartProvided(): void
    {
        self::assertFalse(
            EmailAddress::isValid(
                'a..b@gmail.com.'
            )
        );
    }

    public function testIsValidShouldReturnFalseWhenEmailWithTwoDotsInDomainPartProvided(): void
    {
        self::assertFalse(
            EmailAddress::isValid(
                'a@gmail..com.'
            )
        );
    }

    #[DataProvider('getEmailAddressWithAllowedSpecialCharacters')]
    public function testIsValidShouldReturnTrueWhenEmailWithAllowedSpecialCharactersProvided(string $rawEmailAddress): void
    {
        self::assertTrue(EmailAddress::isValid($rawEmailAddress));
    }

    public function testIsValidShouldReturnFalseWhenEmailWithParenthesisProvidedAndParenthesisNotAllowed(): void
    {
        self::assertFalse(
            EmailAddress::isValid(
                '"ab"@gmail.com',
            )
        );
    }

    public function testIsValidShouldReturnTrueWhenEmailWithParenthesisProvidedAndParenthesisAllowed(): void
    {
        self::assertTrue(
            EmailAddress::isValid(
                '"ab"@gmail.com',
                true
            )
        );

        self::assertTrue(
            EmailAddress::isValid(
                '""@gmail.com',
                true
            )
        );
    }

    public function testIsValidShouldReturnFalseWhenEmailWithInvalidParenthesisProvidedAndParenthesisAllowed(): void
    {
        self::assertFalse(
            EmailAddress::isValid(
                '"ab@gmail.com',
                true
            )
        );

        self::assertFalse(
            EmailAddress::isValid(
                'ab"@gmail.com',
                true
            )
        );

        self::assertFalse(
            EmailAddress::isValid(
                'a"b"@gmail.com',
                true
            )
        );

        self::assertFalse(
            EmailAddress::isValid(
                '"a"b@gmail.com',
                true
            )
        );

        self::assertFalse(
            EmailAddress::isValid(
                'a"b@gmail.com',
                true
            )
        );

        self::assertFalse(
            EmailAddress::isValid(
                '"@gmail.com',
                true
            )
        );

        self::assertFalse(
            EmailAddress::isValid(
                '@gmail.com',
                true
            )
        );
    }

    public function testEqualsShouldReturnTrueWhenEmailAddressWithSameEmailProvided(): void
    {
        $firstEmailAddress = EmailAddress::fromString('test@example.com');
        $secondEmailAddress = EmailAddress::fromString('test@example.com');

        self::assertTrue($firstEmailAddress->equals($secondEmailAddress));
    }

    public function testEqualsShouldReturnFalseWhenEmailAddressWithSameDifferentEmailProvided(): void
    {
        $firstEmailAddress = EmailAddress::fromString('test@example.com');
        $secondEmailAddress = EmailAddress::fromString('jack@example.com');

        self::assertFalse($firstEmailAddress->equals($secondEmailAddress));
    }

    public function testEqualsShouldReturnFalseWhenEmailAddressWithEmailStringProvided(): void
    {
        $firstEmailAddress = EmailAddress::fromString('test@example.com');

        self::assertFalse($firstEmailAddress->equals('test@example.com'));
    }

    public function testValidateShouldThrowLogicExceptionWhenInvalidEmailAddressProvided(): void
    {
        $this->expectException(LogicException::class);

        EmailAddress::validate('@example.com');
    }

    public function testToStringShouldReturnEmailAddress(): void
    {
        $emailAddress = EmailAddress::fromString('test@example.com');

        self::assertSame('test@example.com', $emailAddress->toString());
    }

    public function testLocalPartShouldReturnLocalPartOfEmailAddress(): void
    {
        $emailAddress = EmailAddress::fromString('test@example.com');

        self::assertSame('test', $emailAddress->localPart());
    }

    public function testDomainPartShouldReturnDomainPartOfEmailAddress(): void
    {
        $emailAddress = EmailAddress::fromString('test@example.com');

        self::assertSame('example.com', $emailAddress->domainPart());
    }
}
