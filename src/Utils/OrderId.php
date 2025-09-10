<?php declare(strict_types=1);

namespace Wipop\Utils;

final class OrderId
{
    const VALIDATION_REGEX = '^\d{4}[a-zA-Z0-9]{8}$';
    private readonly string $orderId;

    /**
     * @param string $orderId
     */
    public function __construct(string $orderId)
    {
        if (!$this->isValid($orderId)) {
            throw new \InvalidArgumentException("Order id '$orderId' is not a valid order id.");
        }

        $this->orderId = $orderId;
    }

    /**
     * @return string
     */
    public function getOrderId(): string
    {
        return $this->orderId;
    }

    /**
     * @param string $orderId
     * @return self
     */
    public static function fromString(string $orderId) : self
    {
        return new self($orderId);
    }

    /**
     * @param string $orderId
     * @return bool
     */
    private function isValid(string $orderId) : bool
    {
        return preg_match('/' . self::VALIDATION_REGEX . '/', $orderId) === 1;
        // return preg_match(self::VALIDATION_REGEX, $orderId);
    }
}
