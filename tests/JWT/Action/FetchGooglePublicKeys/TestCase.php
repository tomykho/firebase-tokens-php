<?php

declare(strict_types=1);

namespace Kreait\Firebase\JWT\Tests\Action\FetchGooglePublicKeys;

use DateTimeImmutable;
use Kreait\Firebase\JWT\Action\FetchGooglePublicKeys;
use Kreait\Firebase\JWT\Action\FetchGooglePublicKeys\Handler;
use Lcobucci\Clock\FrozenClock;

/**
 * @internal
 */
abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    abstract protected function createHandler(): Handler;

    protected FrozenClock $clock;

    protected FetchGooglePublicKeys $action;

    protected function setUp(): void
    {
        $now = new DateTimeImmutable();
        $now = $now->setTimestamp($now->getTimestamp()); // Trim microseconds, just to be sure
        $this->clock = new FrozenClock($now);

        $this->action = FetchGooglePublicKeys::fromUrl('bogus');
    }

    /**
     * @test
     */
    public function it_returns_keys()
    {
        $this->createHandler()->handle($this->action);
        $this->addToAssertionCount(1);
    }
}
