<?php

declare(strict_types=1);

namespace Kreait\Firebase\JWT;

final class Token implements Contract\Token
{
    private string $encodedString;

    /** @var array<string, mixed> */
    private array $headers = [];

    /** @var array<string, mixed> */
    private array $payload = [];

    private function __construct()
    {
    }

    /**
     * @param array<string, mixed> $headers
     * @param array<string, mixed> $payload
     */
    public static function withValues(string $encodedString, array $headers, array $payload): self
    {
        $token = new self();

        $token->encodedString = $encodedString;
        $token->headers = $headers;
        $token->payload = $payload;

        return $token;
    }

    /**
     * @return array<string, mixed>
     */
    public function headers(): array
    {
        return $this->headers;
    }

    /**
     * @return array<string, mixed>
     */
    public function payload(): array
    {
        return $this->payload;
    }

    public function toString(): string
    {
        return $this->encodedString;
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
