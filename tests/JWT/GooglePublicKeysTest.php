<?php

declare(strict_types=1);

namespace Kreait\Firebase\JWT\Tests;

use DateInterval;
use DateTimeImmutable;
use Kreait\Firebase\JWT\Action\FetchGooglePublicKeys\Handler;
use Kreait\Firebase\JWT\GooglePublicKeys;
use Kreait\Firebase\JWT\Keys\ExpiringKeys;
use Kreait\Firebase\JWT\Keys\StaticKeys;
use Lcobucci\Clock\FrozenClock;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class GooglePublicKeysTest extends TestCase
{
    private $handler;

    private FrozenClock $clock;

    private GooglePublicKeys $keys;

    private ExpiringKeys $expiringResult;

    private StaticKeys $staticResult;

    protected function setUp(): void
    {
        $now = new DateTimeImmutable();
        $now = $now->setTimestamp($now->getTimestamp()); // Trim microseconds, just to be sure

        $this->clock = new FrozenClock($now);
        $this->handler = $this->createMock(Handler::class);

        $this->expiringResult = ExpiringKeys::withValuesAndExpirationTime(['ir' => 'relevant'], $this->clock->now()->modify('+1 hour'));
        $this->staticResult = StaticKeys::withValues(['ir' => 'relevant']);

        $this->keys = new GooglePublicKeys($this->handler, $this->clock);
    }

    /**
     * @test
     */
    public function it_fetches_keys_only_the_first_time()
    {
        $this->handler->expects($this->once())->method('handle')->willReturn($this->expiringResult);

        $this->assertSame($this->expiringResult->all(), $this->keys->all());
        $this->assertSame($this->expiringResult->all(), $this->keys->all());
    }

    /**
     * @test
     */
    public function it_re_fetches_keys_when_they_are_expired()
    {
        $this->handler->expects($this->exactly(2))->method('handle')->willReturn($this->expiringResult);

        $this->keys->all();
        $this->clock->setTo($this->clock->now()->add(new DateInterval('PT2H')));
        $this->keys->all();
    }

    /**
     * @test
     */
    public function it_uses_non_expiring_keys_forever()
    {
        $this->handler->expects($this->once())->method('handle')->willReturn($this->staticResult);

        $this->assertSame($this->staticResult->all(), $this->keys->all());
        $this->assertSame($this->staticResult->all(), $this->keys->all());
    }
}
