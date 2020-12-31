<?php

declare(strict_types=1);

namespace Kreait\Firebase\JWT;

use Kreait\Firebase\JWT\Action\FetchGooglePublicKeys;
use Kreait\Firebase\JWT\Action\FetchGooglePublicKeys\Handler;
use Kreait\Firebase\JWT\Contract\Expirable;
use Kreait\Firebase\JWT\Contract\Keys;
use Lcobucci\Clock\Clock;

final class GooglePublicKeys implements Keys
{
    /** @var Clock */
    private $clock;

    /** @var Handler */
    private $handler;

    /** @var Keys|null */
    private $keys;

    public function __construct(Handler $handler, Clock $clock)
    {
        $this->handler = $handler;
        $this->clock = $clock;
    }

    public function all(): array
    {
        $keysAreThereButExpired = $this->keys instanceof Expirable && $this->keys->isExpiredAt($this->clock->now());

        if (!$this->keys || $keysAreThereButExpired) {
            $this->keys = $this->handler->handle(FetchGooglePublicKeys::fromGoogle());
            // There is a small chance that we get keys that are already expired, but at this point we're happy
            // that we got keys at all. The next time this method gets called, we will re-fetch.
        }

        return $this->keys->all();
    }
}
