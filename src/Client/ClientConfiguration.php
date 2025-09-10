<?php declare(strict_types=1);

namespace Wipop\Client;

final class ClientConfiguration
{
    public const SANDBOX_API_URL = 'https://sand-api.wipop.es';
    public const PRODUCTION_API_URL = 'https://api.wipop.es';

    /**
     * @param string $environment
     * @param string $merchantId
     * @param string $secretKey
     * @param ClientHttpConfiguration $httpConfiguration
     */
    public function __construct(
      private readonly string $environment,
      private readonly string $merchantId,
      private readonly string $secretKey,
      private readonly ClientHttpConfiguration $httpConfiguration = new ClientHttpConfiguration()
    ) {}

    /**
     * @return string
     */
    public function getEnvironment(): string
    {
        return $this->environment;
    }

    /**
     * @return string
     */
    public function getMerchantId(): string
    {
        return $this->merchantId;
    }

    /**
     * @return string
     */
    public function getSecretKey(): string
    {
        return $this->secretKey;
    }

    /**
     * @return ClientHttpConfiguration
     */
    public function getHttpConfiguration(): ClientHttpConfiguration
    {
        return $this->httpConfiguration;
    }

    /**
     * @return string
     */
    public function getApiUrl(): string
    {
        if ($this->environment == Environment::PRODUCTION) {
            return self::PRODUCTION_API_URL;
        }

        return self::SANDBOX_API_URL;
    }
}