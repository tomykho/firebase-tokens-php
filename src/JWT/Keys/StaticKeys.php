<?php

declare(strict_types=1);

namespace Kreait\Firebase\JWT\Keys;

use Kreait\Firebase\JWT\Contract\Keys;

final class StaticKeys implements Keys
{
    /** @var array<string, string> */
    private array $values = [];

    private function __construct()
    {
    }

    public static function empty(): self
    {
        return new self();
    }

    /**
     * @param array<string, string> $values
     */
    public static function withValues(array $values): self
    {
        $keys = new self();
        $keys->values = $values;

        return $keys;
    }

    /**
     * @return array<string, string>
     */
    public function all(): array
    {
        return $this->values;
    }
}
