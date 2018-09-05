<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Meritoo\FlashBundle\Twig;

use Meritoo\FlashBundle\Service\FlashMessageService;
use Twig\Extension\RuntimeExtensionInterface;

/**
 * Runtime class related to ConfigurationExtension Twig Extension.
 * Required to create lazy-loaded Twig Extension.
 *
 * @author    Meritoo <github@meritoo.pl>
 * @copyright Meritoo
 */
class ConfigurationRuntime implements RuntimeExtensionInterface
{
    /**
     * Service related to flash messages
     *
     * @var FlashMessageService
     */
    private $flashMessageService;

    /**
     * CSS classes for the container for flash messages (with all flash messages)
     *
     * @var string
     */
    private $containerCssClasses;

    /**
     * CSS classes, template for CSS classes actually, for one flash message.
     * Placeholder is used to enter type of flash message.
     *
     * @var string
     */
    private $oneFlashMessageCssClasses;

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
     * @param FlashMessageService $flashMessageService       Service related to flash messages
     * @param string              $containerCssClasses       CSS classes for the container for flash messages (with all
     *                                                       flash messages)
     * @param string              $oneFlashMessageCssClasses CSS classes, template for CSS classes actually, for one
     *                                                       flash message. Placeholder is used to enter type of flash
     *                                                       message.
     * @param string              $positiveFlashMessageType  Type of positive flash message
     * @param string              $negativeFlashMessageType  Type of negative flash message
     * @param string              $neutralFlashMessageType   Type of neutral flash message
     */
    public function __construct(
        FlashMessageService $flashMessageService,
        string $containerCssClasses,
        string $oneFlashMessageCssClasses,
        string $positiveFlashMessageType,
        string $negativeFlashMessageType,
        string $neutralFlashMessageType
    ) {
        $this->flashMessageService = $flashMessageService;
        $this->containerCssClasses = $containerCssClasses;
        $this->oneFlashMessageCssClasses = $oneFlashMessageCssClasses;

        $this
            ->flashMessageService
            ->verifyFlashMessageTypes([
                $positiveFlashMessageType,
                $negativeFlashMessageType,
                $neutralFlashMessageType,
            ]);

        $this->positiveFlashMessageType = $positiveFlashMessageType;
        $this->negativeFlashMessageType = $negativeFlashMessageType;
        $this->neutralFlashMessageType = $neutralFlashMessageType;
    }

    /**
     * Returns CSS classes for the container for flash messages (with all flash messages)
     *
     * @return string
     */
    public function getContainerCssClasses(): string
    {
        return $this->containerCssClasses;
    }

    /**
     * Returns CSS classes for one flash message
     *
     * @param string $flashMessageType Type of flash message, e.g. "warning"
     * @return string
     */
    public function getOneFlashMessageCssClasses(string $flashMessageType): string
    {
        $this->flashMessageService->verifyFlashMessageType($flashMessageType);

        /*
         * Is it a template?
         * Let's replace placeholder with type of flash message
         */
        if ((bool)preg_match('/%s/', $this->oneFlashMessageCssClasses)) {
            return sprintf($this->oneFlashMessageCssClasses, $flashMessageType);
        }

        return $this->oneFlashMessageCssClasses;
    }

    /**
     * Returns type of positive flash message
     *
     * @return string
     */
    public function getPositiveFlashMessageType(): string
    {
        return $this->positiveFlashMessageType;
    }

    /**
     * Returns type of positive flash message
     *
     * @return string
     */
    public function getNegativeFlashMessageType(): string
    {
        return $this->negativeFlashMessageType;
    }

    /**
     * Returns type of positive flash message
     *
     * @return string
     */
    public function getNeutralFlashMessageType(): string
    {
        return $this->neutralFlashMessageType;
    }
}
