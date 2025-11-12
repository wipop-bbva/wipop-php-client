<?php

declare(strict_types=1);

namespace Wipop\Merchant;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Wipop\Client\ClientConfiguration;
use Wipop\Client\Http\HttpClientInterface;
use Wipop\Client\Operation\AbstractOperation;
use Wipop\Utils\Terminal;

use function array_values;
use function sprintf;

/**
 * Operations that expose merchant metadata such as supported payment methods.
 */
final class MerchantOperation extends AbstractOperation
{
    public function __construct(
        HttpClientInterface $httpClient,
        ClientConfiguration $configuration,
        ?LoggerInterface $logger = null,
    ) {
        parent::__construct($httpClient, $configuration, $logger ?? new NullLogger());
    }

    /**
     * Returns the payment methods enabled for the merchant + terminal combination.
     *
     * @return string[]
     */
    public function listPaymentMethods(string $productType, Terminal $terminal): array
    {
        $merchantId = $this->getConfiguration()->getMerchantId();

        $response = $this->get(
            sprintf('/v1/%s/payment_methods', $merchantId),
            [
                'product_type' => $productType,
                'terminal' => $terminal->getId(),
            ]
        );

        return array_values($response);
    }
}
