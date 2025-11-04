<?php

declare(strict_types=1);

namespace Wipop\Tests\Client;

use GuzzleHttp\ClientInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UriInterface;
use ReflectionObject;
use ReflectionProperty;
use Wipop\Client\ClientConfiguration;
use Wipop\Client\Environment;
use Wipop\Client\Http\GuzzleHttpClient;
use Wipop\Client\WipopClient;

use function is_array;

/**
 * @internal
 */
#[CoversClass(WipopClient::class)]
class WipopClientTest extends TestCase
{
    #[Test]
    public function itConfiguresDefaultHttpClientFromConfiguration(): void
    {
        $configuration = new ClientConfiguration(
            Environment::SANDBOX,
            'm1234567890123456789',
            'sk_test_secret'
        );

        $client = new WipopClient($configuration);
        $httpClient = $this->extractHttpClient($client);

        $this->assertInstanceOf(GuzzleHttpClient::class, $httpClient);
        $guzzleClient = $this->extractGuzzleClient($httpClient);
        $this->assertInstanceOf(ClientInterface::class, $guzzleClient);
        $config = $this->extractHttpClientConfig($guzzleClient);

        $this->assertArrayHasKey('base_uri', $config);
        $this->assertInstanceOf(UriInterface::class, $config['base_uri']);
        $this->assertSame('https://sand-api.wipop.es', (string) $config['base_uri']);
        $this->assertArrayHasKey('timeout', $config);
        $this->assertIsInt($config['timeout']);
        $this->assertSame(30, $config['timeout']);
        $this->assertArrayHasKey('connect_timeout', $config);
        $this->assertIsInt($config['connect_timeout']);
        $this->assertSame(5, $config['connect_timeout']);

        $this->assertArrayHasKey('headers', $config);
        $this->assertIsArray($config['headers']);
        /** @var array<string, string> $headers */
        $headers = $config['headers'];
        $this->assertArrayHasKey('Content-Type', $headers);
        $this->assertArrayHasKey('Authorization', $headers);
        $this->assertSame('application/json', $headers['Content-Type']);
        $this->assertSame('Basic ' . base64_encode('sk_test_secret'), $headers['Authorization']);

        $this->assertSame($configuration, $client->getConfiguration());
    }

    private function extractHttpClient(WipopClient $client): GuzzleHttpClient
    {
        $reflection = new ReflectionProperty($client, 'httpClient');
        $reflection->setAccessible(true);

        $httpClient = $reflection->getValue($client);

        if (!$httpClient instanceof GuzzleHttpClient) {
            $this->fail('Unexpected HTTP client implementation.');
        }

        return $httpClient;
    }

    private function extractGuzzleClient(GuzzleHttpClient $client): ClientInterface
    {
        $reflection = new ReflectionProperty($client, 'client');
        $reflection->setAccessible(true);

        $guzzleClient = $reflection->getValue($client);

        if (!$guzzleClient instanceof ClientInterface) {
            $this->fail('Unable to access the underlying Guzzle client.');
        }

        return $guzzleClient;
    }

    /**
     * @return array<string, mixed>
     */
    private function extractHttpClientConfig(ClientInterface $httpClient): array
    {
        $reflection = new ReflectionObject($httpClient);

        if (!$reflection->hasProperty('config')) {
            $this->fail('Unable to access the Guzzle client configuration.');
        }

        $property = $reflection->getProperty('config');
        $property->setAccessible(true);

        $config = $property->getValue($httpClient);

        if (!is_array($config)) {
            $this->fail('Unexpected HTTP client configuration type.');
        }

        // @var array<string, mixed> $config
        return $config;
    }
}
