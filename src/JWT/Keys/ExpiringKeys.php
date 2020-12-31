<?php

declare(strict_types=1);

namespace Kreait\Firebase\JWT\Keys;

use DateTimeImmutable;
use DateTimeInterface;
use Kreait\Firebase\JWT\Contract\Expirable;
use Kreait\Firebase\JWT\Contract\Keys;

final class ExpiringKeys implements Expirable, Keys
{
    /** @var array<string, string> */
    private $values = [];

    /** @var DateTimeImmutable */
    private $expirationTime;

    private function __construct()
    {
        $this->expirationTime = new DateTimeImmutable('0001-01-01'); // Very distant past :)
    }

    /**
     * @param array<string, mixed> $values
     */
    public static function withValuesAndExpirationTime(array $values, DateTimeImmutable $expirationTime): self
    {
        $keys = new self();
        $keys->values = $values;
        $keys->expirationTime = $expirationTime;

        return $keys;
    }

    public function withExpirationTime(DateTimeImmutable $expirationTime): Expirable
    {
        $expirable = clone $this;
        $expirable->expirationTime = $expirationTime;

        return $expirable;
    }

    public function isExpiredAt(DateTimeInterface $now): bool
    {
        return $this->expirationTime < $now;
    }

    public function expiresAt(): DateTimeImmutable
    {
        return $this->expirationTime;
    }

    /**
     * @return array<string, string>
     */
    public function all(): array
    {
        return $this->values;
    }
}
