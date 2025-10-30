<?php

declare(strict_types=1);

namespace Wipop\Client\Http;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use Wipop\Client\Exception\HttpTransportException;

use function sprintf;

final class GuzzleHttpClient implements HttpClientInterface
{
    public function __construct(private readonly ClientInterface $client)
    {
    }

    /**
     * @param array<string, mixed> $options
     */
    public function request(string $method, string $uri, array $options = []): ResponseInterface
    {
        try {
            return $this->client->request($method, $uri, $options);
        } catch (GuzzleException $exception) {
            throw new HttpTransportException(
                sprintf('HTTP transport error on %s %s', $method, $uri),
                null,
                $exception
            );
        }
    }
}
