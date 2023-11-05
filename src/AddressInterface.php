<?php

/**
 * TOBENTO
 *
 * @copyright   Tobias Strub, TOBENTO
 * @license     MIT License, see LICENSE file distributed with this source code.
 * @author      Tobias Strub
 * @link        https://www.tobento.ch
 */

declare(strict_types=1);

namespace Tobento\Service\Mail;

use JsonSerializable;

/**
 * AddressInterface
 */
interface AddressInterface extends JsonSerializable
{
    /**
     * Returns the email.
     *
     * @return string
     */
    public function email(): string;
    
    /**
     * Returns the name.
     *
     * @return null|string
     */
    public function name(): null|string;
}