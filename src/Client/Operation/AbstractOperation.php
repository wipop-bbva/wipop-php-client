<?php

declare(strict_types=1);

namespace Wipop\Client\Operation;

use JsonException;
use Psr\Log\LoggerInterface;
use Wipop\Client\ClientConfiguration;
use Wipop\Client\Exception\HttpTransportException;
use Wipop\Client\Exception\WipopApiException;
use Wipop\Client\Exception\WipopApiExceptionFactory;
use Wipop\Client\Http\HttpClientInterface;

use function is_array;
use function json_decode;
use function sprintf;

/**
 * Base class for centralized Wipop HTTP requests, error handling and JSON decoding.
 */
abstract class AbstractOperation
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly ClientConfiguration $configuration,
        private readonly LoggerInterface $logger
    ) {
    }

    protected function getConfiguration(): ClientConfiguration
    {
        return $this->configuration;
    }

    protected function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    /**
     * It sends a POST request and returns the decoded JSON body.
     *
     * @param array<string, mixed> $payload
     * @param array<string, mixed> $query
     *
     * @return array<string, mixed>
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
     * @return array<string, mixed>
     */
    private function request(string $method, string $path, array $options): array
    {
        try {
            $response = $this->httpClient->request($method, $path, $options);
        } catch (HttpTransportException $exception) {
            $message = sprintf('Error calling %s %s: %s', $method, $path, $exception->getMessage());
            $this->logger->error($message);

            throw new WipopApiException($message, null, $exception);
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

            throw new WipopApiException($message, null, $exception);
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
