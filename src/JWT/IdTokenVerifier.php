<?php

declare(strict_types=1);

namespace Kreait\Firebase\JWT;

use GuzzleHttp\Client;
use InvalidArgumentException;
use Kreait\Firebase\JWT\Action\FetchGooglePublicKeys;
use Kreait\Firebase\JWT\Action\VerifyIdToken;
use Kreait\Firebase\JWT\Cache\InMemoryCache;
use Kreait\Firebase\JWT\Contract\Token;
use Kreait\Firebase\JWT\Error\IdTokenVerificationFailed;
use Lcobucci\Clock\SystemClock;
use Psr\Cache\CacheItemPoolInterface;
use Psr\SimpleCache\CacheInterface;

final class IdTokenVerifier
{
    /** @var VerifyIdToken\Handler */
    private $handler;

    /** @var string|null */
    private $expectedTenantId;

    public function __construct(VerifyIdToken\Handler $handler)
    {
        $this->handler = $handler;
    }

    public static function createWithProjectId(string $projectId): self
    {
        return self::createWithProjectIdAndCache($projectId, InMemoryCache::createEmpty());
    }

    /**
     * @param CacheInterface|CacheItemPoolInterface $cache
     */
    public static function createWithProjectIdAndCache(string $projectId, $cache): self
    {
        $clock = SystemClock::fromUTC();
        $baseKeyHandler = new FetchGooglePublicKeys\WithGuzzle(new Client(['http_errors' => false]), $clock);

        $keyHandler = $cache instanceof CacheInterface
            ? new FetchGooglePublicKeys\WithPsr16SimpleCache($baseKeyHandler, $cache, $clock)
            : new FetchGooglePublicKeys\WithPsr6Cache($baseKeyHandler, $cache, $clock);

        $keys = new GooglePublicKeys($keyHandler, $clock);
        $handler = new VerifyIdToken\WithLcobucciJWT($projectId, $keys, $clock);

        return new self($handler);
    }

    public function withExpectedTenantId(string $tenantId): self
    {
        $generator = clone $this;
        $generator->expectedTenantId = $tenantId;

        return $generator;
    }

    public function execute(VerifyIdToken $action): Token
    {
        if ($this->expectedTenantId) {
            $action = $action->withExpectedTenantId($this->expectedTenantId);
        }

        return $this->handler->handle($action);
    }

    /**
     * @throws IdTokenVerificationFailed
     */
    public function verifyIdToken(string $token): Token
    {
        return $this->execute(VerifyIdToken::withToken($token));
    }

    /**
     * @throws InvalidArgumentException on invalid leeway
     * @throws IdTokenVerificationFailed
     */
    public function verifyIdTokenWithLeeway(string $token, int $leewayInSeconds): Token
    {
        return $this->execute(VerifyIdToken::withToken($token)->withLeewayInSeconds($leewayInSeconds));
    }
}
