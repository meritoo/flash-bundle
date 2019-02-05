<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Meritoo\FlashBundle\Service;

use Meritoo\Common\Utilities\Arrays;
use Meritoo\CommonBundle\Service\Base\BaseService;
use Meritoo\FlashBundle\Exception\UnavailableFlashMessageTypeException;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Service related to flash messages
 *
 * @author    Meritoo <github@meritoo.pl>
 * @copyright Meritoo
 */
class FlashMessageService extends BaseService
{
    /**
     * The session
     *
     * @var SessionInterface
     */
    private $session;

    /**
     * All available types of flash message
     *
     * @var array
     */
    private $availableFlashMessageTypes;

    /**
     * Type of positive flash message
     *
     * @var string
     */
    private $positiveFlashMessageType;

    /**
     * Type of negative flash message
     *
     * @var string
     */
    private $negativeFlashMessageType;

    /**
     * Type of neutral flash message
     *
     * @var string
     */
    private $neutralFlashMessageType;

    /**
     * Class constructor
     *
     * @param SessionInterface $session                    The session
     * @param array            $availableFlashMessageTypes All available types of flash message
     * @param string           $positiveFlashMessageType   Type of positive flash message
     * @param string           $negativeFlashMessageType   Type of negative flash message
     * @param string           $neutralFlashMessageType    Type of neutral flash message
     */
    public function __construct(
        SessionInterface $session,
        array $availableFlashMessageTypes,
        string $positiveFlashMessageType,
        string $negativeFlashMessageType,
        string $neutralFlashMessageType
    ) {
        $this->session = $session;
        $this->availableFlashMessageTypes = $availableFlashMessageTypes;
        $this->positiveFlashMessageType = $positiveFlashMessageType;
        $this->negativeFlashMessageType = $negativeFlashMessageType;
        $this->neutralFlashMessageType = $neutralFlashMessageType;
    }

    /**
     * Prepares given flash messages:
     * - verifies types of flash messages
     * - makes sure that messages of given type are passed as an array
     *
     * @param array $messages Flash messages to verify. Key-value pairs:
     *                        - key - type of flash message
     *                        - value - flash message
     */
    public function prepareMessages(array &$messages): void
    {
        /*
         * No messages provided?
         * Nothing to do
         */
        if (empty($messages)) {
            return;
        }

        // Verify types of flash messages
        $types = array_keys($messages);
        $this->verifyFlashMessageTypes($types);

        // Make sure that messages of given type are passed as an array
        $this->makeArrayOfMessages($messages);
    }

    /**
     * Adds flash messages (to bag/container stored in session)
     *
     * @param array $messages Flash messages to add. Key-value pairs:
     *                        - key - type of flash message
     *                        - value - flash message or many flash messages
     * @return $this
     */
    public function addFlashMessages(array $messages): FlashMessageService
    {
        /*
         * No messages provided?
         * Nothing to do
         */
        if (empty($messages)) {
            return $this;
        }

        // Prepare the flash messages
        $this->prepareMessages($messages);

        /** @var Session $session */
        $session = $this->session;
        $flashBag = $session->getFlashBag();

        // Add flash messages (to bag/container stored in session)
        foreach ($messages as $type => $messagesOfType) {
            foreach ($messagesOfType as $message) {
                $flashBag->add($type, $message);
            }
        }

        return $this;
    }

    /**
     * Adds positive flash messages (to bag/container stored in session)
     *
     * @param array $messages Flash messages to add. Without type, only texts.
     * @return FlashMessageService
     */
    public function addPositiveFlashMessages(array $messages): FlashMessageService
    {
        $messagesWithType = $this->applyTypeForMessages($messages, $this->positiveFlashMessageType);

        return $this->addFlashMessages($messagesWithType);
    }

    /**
     * Adds negative flash messages (to bag/container stored in session)
     *
     * @param array $messages Flash messages to add. Without type, only texts.
     * @return FlashMessageService
     */
    public function addNegativeFlashMessages(array $messages): FlashMessageService
    {
        $messagesWithType = $this->applyTypeForMessages($messages, $this->negativeFlashMessageType);

        return $this->addFlashMessages($messagesWithType);
    }

    /**
     * Adds neutral flash messages (to bag/container stored in session)
     *
     * @param array $messages Flash messages to add. Without type, only texts.
     * @return FlashMessageService
     */
    public function addNeutralFlashMessages(array $messages): FlashMessageService
    {
        $messagesWithType = $this->applyTypeForMessages($messages, $this->neutralFlashMessageType);

        return $this->addFlashMessages($messagesWithType);
    }

    /**
     * Verifies if given type of flash message is available/correct
     *
     * @param string $flashMessageType Type of flash message to verify, e.g. "warning"
     * @throws UnavailableFlashMessageTypeException
     * @return FlashMessageService
     */
    public function verifyFlashMessageType(string $flashMessageType): FlashMessageService
    {
        // Oops, type of flash message is not correct
        if (false === $this->isAvailableFlashMessageType($flashMessageType)) {
            throw UnavailableFlashMessageTypeException::create($flashMessageType, $this->availableFlashMessageTypes);
        }

        return $this;
    }

    /**
     * Verifies if given types of flash message is available/correct
     *
     * @param array $flashMessageTypes Types of flash message to verify, e.g. ["warning", "success"]
     * @return FlashMessageService
     */
    public function verifyFlashMessageTypes(array $flashMessageTypes): FlashMessageService
    {
        /*
         * No types provided?
         * Nothing to do
         */
        if (empty($flashMessageTypes)) {
            return $this;
        }

        foreach ($flashMessageTypes as $type) {
            $this->verifyFlashMessageType($type);
        }

        return $this;
    }

    /**
     * Returns information if there are any flash messages to display (in bag/container stored in session)
     *
     * @return bool
     */
    public function hasFlashMessages(): bool
    {
        /** @var Session $session */
        $session = $this->session;
        $allMessages = $session->getFlashBag()->peekAll();

        return \count($allMessages) > 0;
    }

    /**
     * Returns information if type of flash message is available/correct
     *
     * @param string $flashMessageType Type of flash message to verify, e.g. "warning"
     * @return bool
     */
    private function isAvailableFlashMessageType(string $flashMessageType): bool
    {
        return \in_array($flashMessageType, $this->availableFlashMessageTypes, true);
    }

    /**
     * Make sure that messages of given type are passed as an array
     *
     * @param array $messages Flash messages to verify
     */
    private function makeArrayOfMessages(array &$messages): void
    {
        foreach ($messages as $type => $messagesOfType) {
            $messagesArray = Arrays::makeArray($messagesOfType);
            $messages[$type] = $messagesArray;
        }
    }

    /**
     * Applies given type of flash messages for the given flash messages
     *
     * @param array  $messages     Flash messages. Without type, only texts.
     * @param string $messagesType Type of flash messages to apply
     * @return array
     */
    private function applyTypeForMessages(array $messages, string $messagesType): array
    {
        /*
         * No messages provided?
         * Nothing to do
         */
        if (empty($messages)) {
            return [];
        }

        return [
            $messagesType => $messages,
        ];
    }
}
