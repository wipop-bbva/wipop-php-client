<?php

declare(strict_types=1);

namespace Wipop\Client\Operation;

use JsonException;
use Psr\Log\LoggerInterface;
use Wipop\Client\Exception\HttpTransportException;
use Wipop\Client\Exception\WipopApiExceptionFactory;
use Wipop\Client\Http\HttpClientInterface;
use Wipop\Client\WipopClientConfiguration;
use Wipop\Exception\WipopException;
use Wipop\Serializer\Hydrator;

use function ini_get;
use function ini_set;
use function is_array;
use function json_decode;
use function json_encode;
use function sprintf;

use const JSON_THROW_ON_ERROR;

/**
 * Base class for centralized Wipop HTTP requests, error handling and JSON decoding.
 */
abstract class AbstractOperation
{
    private Hydrator $hydrator;

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly WipopClientConfiguration $configuration,
        private readonly LoggerInterface $logger,
        ?Hydrator $hydrator = null,
    ) {
        $this->hydrator = $hydrator ?? new Hydrator();
    }

    protected function getConfiguration(): WipopClientConfiguration
    {
        return $this->configuration;
    }

    protected function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    /**
     * @template T of object
     *
     * @param class-string<T>     $className
     * @param array<string,mixed> $data
     *
     * @return T
     */
    protected function hydrate(string $className, array $data): object
    {
        return $this->hydrator->hydrate($className, $data);
    }

    /**
     * Sends a GET request and returns the decoded JSON response.
     *
     * @param array<string, mixed> $query
     *
     * @return array<int|string, mixed>
     */
    protected function get(string $path, array $query = []): array
    {
        $options = $this->buildOptions([], $query);

        return $this->request('GET', $path, $options);
    }

    /**
     * It sends a POST request and returns the decoded JSON body.
     *
     * @param array<string, mixed> $payload
     * @param array<string, mixed> $query
     *
     * @return array<int|string, mixed>
     */
    protected function post(string $path, array $payload = [], array $query = []): array
    {
        $options = $this->buildOptions($payload, $query);

        return $this->request('POST', $path, $options);
    }

    /**
     * @param array<string, mixed> $payload
     * @param array<string, mixed> $query
     *
     * @return array<string, mixed>
     */
    private function buildOptions(array $payload, array $query): array
    {
        $options = [];

        if ($payload !== []) {
            $options['body'] = $this->encodeJson($payload);
        }

        if ($query !== []) {
            $options['query'] = $query;
        }

        return $options;
    }

    /**
     * Encodes floats in their shortest form (0.05, not 0.050000000000000003),
     * regardless of the host's serialize_precision.
     *
     * @param array<string, mixed> $payload
     */
    private function encodeJson(array $payload): string
    {
        $precision = (string) ini_get('serialize_precision');
        ini_set('serialize_precision', '-1');

        try {
            return json_encode($payload, JSON_THROW_ON_ERROR);
        } finally {
            ini_set('serialize_precision', $precision);
        }
    }

    /**
     * @param array<string, mixed> $options
     *
     * @return array<int|string, mixed>
     */
    private function request(string $method, string $path, array $options): array
    {
        try {
            $response = $this->httpClient->request($method, $path, $options);
        } catch (HttpTransportException $exception) {
            $message = sprintf('Error calling %s %s: %s', $method, $path, $exception->getMessage());
            $this->logger->error($message);

            throw new WipopException($message, null, $exception);
        }

        try {
            /** @var array<string, mixed> $data */
            $data = json_decode(
                $response->getBody()->getContents(),
                true,
                512,
                JSON_THROW_ON_ERROR
            );
        } catch (JsonException $exception) {
            $message = sprintf('Error decoding JSON response from %s %s: %s', $method, $path, $exception->getMessage());
            $this->logger->error($message);

            throw new WipopException($message, null, $exception);
        }

        $this->assertNoApiError($data);

        return $data;
    }

    /**
     * @param array<string, mixed> $data
     */
    private function assertNoApiError(array $data): void
    {
        $status = $data['status'] ?? null;
        if ($status !== 'ERROR') {
            return;
        }

        $responseCode = $data['response_code'] ?? null;
        if (!is_array($responseCode)) {
            return;
        }

        $this->logger->warning('API error response detected', ['response' => $data]);

        throw WipopApiExceptionFactory::fromPayload($data);
    }
}
