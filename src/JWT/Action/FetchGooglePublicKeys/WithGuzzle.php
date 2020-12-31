<?php

declare(strict_types=1);

namespace Kreait\Firebase\JWT\Action\FetchGooglePublicKeys;

use DateInterval;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Kreait\Firebase\JWT\Action\FetchGooglePublicKeys;
use Kreait\Firebase\JWT\Contract\Keys;
use Kreait\Firebase\JWT\Error\FetchingGooglePublicKeysFailed;
use Kreait\Firebase\JWT\Keys\ExpiringKeys;
use Kreait\Firebase\JWT\Keys\StaticKeys;
use Lcobucci\Clock\Clock;

final class WithGuzzle implements Handler
{
    private ClientInterface $client;

    private Clock $clock;

    public function __construct(ClientInterface $client, Clock $clock)
    {
        $this->client = $client;
        $this->clock = $clock;
    }

    public function handle(FetchGooglePublicKeys $action): Keys
    {
        $url = $action->url();

        try {
            $response = $this->client->request('GET', $url, [
                'http_errors' => false,
                'headers' => [
                    'Content-Type' => 'Content-Type: application/json; charset=UTF-8',
                ],
            ]);
        } catch (GuzzleException $e) {
            throw FetchingGooglePublicKeysFailed::because("The connection to {$url} failed: ".$e->getMessage(), $e->getCode(), $e);
        }

        if (($statusCode = $response->getStatusCode()) !== 200) {
            throw FetchingGooglePublicKeysFailed::because("Unexpected status code {$statusCode}");
        }

        $expiresAt = null;
        $cacheControl = $response->getHeaderLine('Cache-Control');
        if (((int) \preg_match('/max-age=(\d+)/i', $cacheControl, $matches)) === 1) {
            $expiresAt = $this->clock->now()->add(new DateInterval('PT'.$matches[1].'S'));
        }

        $keys = \json_decode((string) $response->getBody(), true);

        if ($expiresAt !== null) {
            return ExpiringKeys::withValuesAndExpirationTime($keys, $expiresAt);
        }

        return StaticKeys::withValues($keys);
    }
}