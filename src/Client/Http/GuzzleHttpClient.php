<?php

declare(strict_types=1);

namespace Wipop\Client\Http;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use Wipop\Client\ClientConfiguration;
use Wipop\Client\Exception\HttpTransportException;

use function array_merge;
use function base64_encode;
use function is_array;
use function sprintf;
use function str_ends_with;

final class GuzzleHttpClient implements HttpClientInterface
{
    private readonly ClientInterface $client;

    /**
     * @param array<string, mixed> $options
     */
    public function __construct(ClientConfiguration $configuration, array $options = [])
    {
        $secretKey = $configuration->getSecretKey();

        if (!str_ends_with($secretKey, ':')) {
            $secretKey .= ':';
        }

        $defaultOptions = [
            'base_uri' => $configuration->getApiUrl(),
            'timeout' => $configuration->getHttpConfiguration()->getResponseTimeout() / 1000,
            'connect_timeout' => $configuration->getHttpConfiguration()->getConnectionRequestTimeout() / 1000,
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => sprintf(
                    'Basic %s',
                    base64_encode($secretKey)
                ),
            ],
        ];

        $this->client = new Client($this->mergeOptions($defaultOptions, $options));
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

    /**
     * @param array<string, mixed> $defaults
     * @param array<string, mixed> $overrides
     *
     * @return array<string, mixed>
     */
    private function mergeOptions(array $defaults, array $overrides): array
    {
        $merged = $defaults;

        foreach ($overrides as $key => $value) {
            if ($key === 'headers' && isset($merged['headers']) && is_array($value)) {
                $merged['headers'] = array_merge($merged['headers'], $value);
                continue;
            }

            $merged[$key] = $value;
        }

        return $merged;
    }
}
