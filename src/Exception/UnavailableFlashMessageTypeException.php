<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Meritoo\FlashBundle\Exception;

/**
 * An exception used while type of flash message is unavailable
 *
 * @author    Meritoo <github@meritoo.pl>
 * @copyright Meritoo
 */
class UnavailableFlashMessageTypeException extends \Exception
{
    /**
     * Creates exception
     *
     * @param string $flashMessageType           Unavailable type of flash message
     * @param array  $availableFlashMessageTypes Available flash message types
     * @return UnavailableFlashMessageTypeException
     */
    public static function create(
        string $flashMessageType,
        array $availableFlashMessageTypes
    ): UnavailableFlashMessageTypeException {
        $template = 'The \'%s\' type of flash message is unavailable. Available types: %s. Can you use one of them?';
        $message = sprintf($template, $flashMessageType, implode(', ', $availableFlashMessageTypes));

        return new self($message);
    }
}
