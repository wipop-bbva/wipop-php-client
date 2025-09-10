<?php declare(strict_types=1);

namespace Wipop\Client;

final class ClientHttpConfiguration
{
    public const DEFAULT_CONNECTION_REQUEST_TIMEOUT = 5000;
    public const DEFAULT_RESPONSE_TIMEOUT = 30000;

    /**
     * @param int $connectionRequestTimeout
     * @param int $responseTimeout
     */
    public function __construct(
      private readonly int $connectionRequestTimeout = self::DEFAULT_CONNECTION_REQUEST_TIMEOUT,
      private readonly int $responseTimeout = self::DEFAULT_RESPONSE_TIMEOUT,
    ) {}

    /**
     * @return int
     */
    public function getConnectionRequestTimeout(): int
    {
        return $this->connectionRequestTimeout;
    }

    /**
     * @return int
     */
    public function getResponseTimeout(): int
    {
        return $this->responseTimeout;
    }
}