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
 * Twig extension that provides functions and filters related to configuration of this bundle
 *
 * @author    Meritoo <github@meritoo.pl>
 * @copyright Meritoo
 */
class ConfigurationExtension extends AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        $functions = [
            1 => [
                ConfigurationRuntime::class,
                'getContainerCssClasses',
            ],
            2 => [
                ConfigurationRuntime::class,
                'getOneFlashMessageCssClasses',
            ],
            3 => [
                ConfigurationRuntime::class,
                'getPositiveFlashMessageType',
            ],
            4 => [
                ConfigurationRuntime::class,
                'getNegativeFlashMessageType',
            ],
            5 => [
                ConfigurationRuntime::class,
                'getNeutralFlashMessageType',
            ],
        ];

        return array_merge(parent::getFunctions(), [
            new TwigFunction('meritoo_flash_container_css_classes', $functions[1]),
            new TwigFunction('meritoo_flash_one_flash_message_css_classes', $functions[2]),
            new TwigFunction('meritoo_flash_positive_flash_message_type', $functions[3]),
            new TwigFunction('meritoo_flash_negative_flash_message_type', $functions[4]),
            new TwigFunction('meritoo_flash_neutral_flash_message_type', $functions[5]),
        ]);
    }
}
