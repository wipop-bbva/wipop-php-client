<?php

declare(strict_types=1);

namespace Wipop\Client\Http;

use Psr\Http\Message\ResponseInterface;
use Wipop\Client\Exception\HttpTransportException;

interface HttpClientInterface
{
    /**
     * @param array<string, mixed> $options
     *
     * @throws HttpTransportException
     */
    public function request(string $method, string $uri, array $options = []): ResponseInterface;
}
