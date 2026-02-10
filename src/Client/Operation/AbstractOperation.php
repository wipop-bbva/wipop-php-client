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

use function is_array;
use function json_decode;
use function sprintf;

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
            $options['json'] = $payload;
        }

        if ($query !== []) {
            $options['query'] = $query;
        }

        return $options;
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
