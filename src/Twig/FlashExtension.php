<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Meritoo\FlashBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Twig extension that provides functions and filters related to flash messages
 *
 * @author    Meritoo <github@meritoo.pl>
 * @copyright Meritoo
 */
class FlashExtension extends AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        $options = [
            'is_safe' => ['html'],
        ];

        $functions = [
            1 => [
                FlashRuntime::class,
                'renderFlashMessages',
            ],
            2 => [
                FlashRuntime::class,
                'renderFlashMessagesFromSession',
            ],
            3 => [
                FlashRuntime::class,
                'hasFlashMessages',
            ],
        ];

        return array_merge(parent::getFunctions(), [
            new TwigFunction('meritoo_flash_message_render_messages', $functions[1], $options),
            new TwigFunction('meritoo_flash_message_render_messages_from_session', $functions[2], $options),
            new TwigFunction('meritoo_flash_message_has_messages', $functions[3]),
        ]);
    }
}
