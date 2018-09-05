<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Meritoo\FlashBundle\DependencyInjection;

use Meritoo\Common\Utilities\Miscellaneous;
use Meritoo\CommonBundle\DependencyInjection\Base\BaseExtension;

/**
 * Dependency Injection (DI) extension for this bundle
 *
 * @author    Meritoo <github@meritoo.pl>
 * @copyright Meritoo <http://www.meritoo.pl>
 */
class MeritooFlashExtension extends BaseExtension
{
    /**
     * {@inheritdoc}
     */
    protected function getBundleDirectoryPath(): string
    {
        return Miscellaneous::concatenatePaths(__DIR__, '..');
    }

    /**
     * {@inheritdoc}
     */
    protected function getKeysToStopLoadingParametersOn(): array
    {
        return array_merge(parent::getKeysToStopLoadingParametersOn(), [
            'available',
        ]);
    }
}
