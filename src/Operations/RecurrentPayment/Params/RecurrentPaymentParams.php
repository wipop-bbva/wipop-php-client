<?php

declare(strict_types=1);

namespace Wipop\Operations\RecurrentPayment\Params;

use Wipop\Domain\PostType;
use Wipop\Domain\PostTypeMode;
use Wipop\Operations\Charge\Params\CreateChargeParams;

/**
 * Parameters for creating recurring charges.
 */
final class RecurrentPaymentParams extends CreateChargeParams
{
    public function __construct()
    {
        $this->postType(new PostType(PostTypeMode::RECURRENT));
    }
}
