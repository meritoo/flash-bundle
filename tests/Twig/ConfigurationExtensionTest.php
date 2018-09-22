<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Meritoo\Test\FlashBundle\Twig;

use Meritoo\CommonBundle\Test\Twig\Base\BaseTwigExtensionTestCase;
use Meritoo\FlashBundle\Twig\ConfigurationExtension;

/**
 * Test case for the Twig extension that provides functions and filters related to configuration of this bundle
 *
 * @author    Meritoo <github@meritoo.pl>
 * @copyright Meritoo
 */
class ConfigurationExtensionTest extends BaseTwigExtensionTestCase
{
    /**
     * @covers \Meritoo\FlashBundle\Twig\ConfigurationExtension::getFunctions
     */
    public function testGetFunctions(): void
    {
        $functions = static::$container
            ->get($this->getExtensionNamespace())
            ->getFunctions();

        static::assertCount(5, $functions);
    }

    public function testContainerCssClassesUsingTestEnvironment(): void
    {
        $this->verifyRenderedTemplate(
            'container_css_classes',
            '{{ meritoo_flash_container_css_classes() }}',
            'all-flash-messages'
        );
    }

    public function testContainerCssClassesUsingDefaults(): void
    {
        static::bootKernel([
            'environment' => 'defaults',
        ]);

        $this->verifyRenderedTemplate(
            'container_css_classes',
            '{{ meritoo_flash_container_css_classes() }}',
            'alerts'
        );
    }

    public function testOneFlashMessageCssClassesUsingTestEnvironment(): void
    {
        $this->verifyRenderedTemplate(
            'one_flash_message_css_classes',
            '{{ meritoo_flash_one_flash_message_css_classes(\'positive\') }}',
            'message positive-message-type single-row'
        );
    }

    public function testOneFlashMessageCssClassesUsingDefaults(): void
    {
        static::bootKernel([
            'environment' => 'defaults',
        ]);

        $this->verifyRenderedTemplate(
            'one_flash_message_css_classes',
            '{{ meritoo_flash_one_flash_message_css_classes(\'success\') }}',
            'alert alert-success'
        );
    }

    public function testGetPositiveFlashMessageTypeUsingTestEnvironment(): void
    {
        $this->verifyRenderedTemplate(
            'positive_flash_message_type',
            '{{ meritoo_flash_positive_flash_message_type() }}',
            'positive'
        );
    }

    public function testGetPositiveFlashMessageTypeUsingDefaults(): void
    {
        static::bootKernel([
            'environment' => 'defaults',
        ]);

        $this->verifyRenderedTemplate(
            'positive_flash_message_type',
            '{{ meritoo_flash_positive_flash_message_type() }}',
            'success'
        );
    }

    public function testGetNegativeFlashMessageTypeUsingTestEnvironment(): void
    {
        $this->verifyRenderedTemplate(
            'negative_flash_message_type',
            '{{ meritoo_flash_negative_flash_message_type() }}',
            'negative'
        );
    }

    public function testGetNegativeFlashMessageTypeUsingDefaults(): void
    {
        static::bootKernel([
            'environment' => 'defaults',
        ]);

        $this->verifyRenderedTemplate(
            'negative_flash_message_type',
            '{{ meritoo_flash_negative_flash_message_type() }}',
            'danger'
        );
    }

    public function testGetNeutralFlashMessageTypeUsingTestEnvironment(): void
    {
        $this->verifyRenderedTemplate(
            'neutral_flash_message_type',
            '{{ meritoo_flash_neutral_flash_message_type() }}',
            'information'
        );
    }

    public function testGetNeutralFlashMessageTypeUsingDefaults(): void
    {
        static::bootKernel([
            'environment' => 'defaults',
        ]);

        $this->verifyRenderedTemplate(
            'neutral_flash_message_type',
            '{{ meritoo_flash_neutral_flash_message_type() }}',
            'info'
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getExtensionNamespace(): string
    {
        return ConfigurationExtension::class;
    }
}
