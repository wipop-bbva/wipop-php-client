<?php

declare(strict_types=1);

namespace Wipop\Client\Exception;

final class ApiErrorCode
{
    /**
     * Uncontrolled error
     */
    public const AC000 = 'AC000';

    /**
     * Commercial error
     */
    public const BC000 = 'BC000';

    /**
     * Data Access error
     */
    public const DC000 = 'DC000';

    /**
     * Invalid data
     */
    public const VC000 = 'VC000';

    /**
     * The operation could not be completed.
     * The operation could not be authenticated.
     */
    public const BC0001 = 'BC0001';

    /**
     * The operation could not be completed.
     * Please check the information you entered.
     */
    public const BC0002 = 'BC0002';

    /**
     * The operation could not be completed.
     * The operation you are trying to perform is not allowed.
     */
    public const BC0007 = 'BC0007';

    /**
     * The operation could not be completed.
     * Please try again later.
     */
    public const BC0009 = 'BC0009';

    /**
     * The operation could not be completed.
     * The time available to complete the operation has expired. Please try again later.
     */
    public const BC0013 = 'BC0013';
}
