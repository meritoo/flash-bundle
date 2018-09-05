<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Meritoo\Test\FlashBundle\Twig;

use Meritoo\CommonBundle\Test\Twig\Base\BaseTwigExtensionTestCase;
use Meritoo\FlashBundle\Twig\FlashExtension;
use Twig\Error\RuntimeError;

/**
 * Test case for the Twig extension that provides functions and filters related to flash messages
 *
 * @author    Meritoo <github@meritoo.pl>
 * @copyright Meritoo
 */
class FlashExtensionTest extends BaseTwigExtensionTestCase
{
    public function testGetFunctions(): void
    {
        $functions = static::$container
            ->get($this->getExtensionNamespace())
            ->getFunctions();

        static::assertCount(2, $functions);
    }

    public function testRenderMessagesWithoutMessages(): void
    {
        $this->verifyRenderedTemplate(
            'render_messages',
            '{{ meritoo_flash_message_render_messages({}) }}',
            ''
        );
    }

    /**
     * @param string $template                 Source code of the rendered template
     * @param string $expectedExceptionMessage Expected message of exception
     *
     * @dataProvider provideTemplateToRenderMessagesWithUnavailableFlashMessageTypeUsingTestEnvironment
     */
    public function testRenderMessagesUsingTestEnvironmentAndUnavailableFlashMessageType(
        string $template,
        string $expectedExceptionMessage
    ): void {
        $this->expectException(RuntimeError::class);
        $this->expectExceptionMessage($expectedExceptionMessage);

        $this->verifyRenderedTemplate(
            'render_messages',
            $template,
            'It does not matter, because an exception is thrown'
        );
    }

    /**
     * @param string $template                 Source code of the rendered template
     * @param string $expectedExceptionMessage Expected message of exception
     *
     * @dataProvider provideTemplateToRenderMessagesWithUnavailableFlashMessageTypeUsingDefaults
     */
    public function testRenderMessagesUsingDefaultsAndUnavailableFlashMessageType(
        string $template,
        string $expectedExceptionMessage
    ): void {
        static::bootKernel([
            'environment' => 'defaults',
        ]);

        $this->expectException(RuntimeError::class);
        $this->expectExceptionMessage($expectedExceptionMessage);

        $this->verifyRenderedTemplate(
            'render_messages',
            $template,
            'It does not matter, because an exception is thrown'
        );
    }

    /**
     * @param string $template Source code of the rendered template
     * @param string $expected Expected result of rendering
     *
     * @dataProvider provideTemplateToRenderMessagesUsingTestEnvironment
     */
    public function testRenderMessagesUsingTestEnvironment(string $template, string $expected): void
    {
        $this->verifyRenderedTemplate('render_messages', $template, $expected);
    }

    /**
     * @param string $template Source code of the rendered template
     * @param string $expected Expected result of rendering
     *
     * @dataProvider provideTemplateToRenderMessagesUsingDefaults
     */
    public function testRenderMessagesUsingDefaults(string $template, string $expected): void
    {
        static::bootKernel([
            'environment' => 'defaults',
        ]);

        $this->verifyRenderedTemplate('render_messages', $template, $expected);
    }

    /**
     * Provide template to render messages with unavailable flash message type using test environment
     *
     * @return \Generator
     */
    public function provideTemplateToRenderMessagesWithUnavailableFlashMessageTypeUsingTestEnvironment(): \Generator
    {
        $messageTemplate = 'An exception has been thrown during the rendering of a template ("The \'%s\' type of flash'
            . ' message is unavailable. Available types: positive, negative, information. Can you use one of them?';

        yield[
            '{{ meritoo_flash_message_render_messages({
                \'test1\': \'Test 1\',
                \'test2\': \'Test 2\'
            }) }}',
            sprintf($messageTemplate, 'test1'),
        ];

        yield[
            '{{ meritoo_flash_message_render_messages({
                \'positive\': \'Saved\',
                \'test2\': \'Test 2\'
            }) }}',
            sprintf($messageTemplate, 'test2'),
        ];

        yield[
            '{{ meritoo_flash_message_render_messages({
                \'test\': \'Test Test Test\'
            }) }}',
            sprintf($messageTemplate, 'test'),
        ];
    }

    /**
     * Provide template to render messages with unavailable flash message type using defaults
     *
     * @return \Generator
     */
    public function provideTemplateToRenderMessagesWithUnavailableFlashMessageTypeUsingDefaults(): \Generator
    {
        $messageTemplate = 'An exception has been thrown during the rendering of a template ("The \'%s\' type of flash'
            . ' message is unavailable. Available types: primary, secondary, success, info, warning, danger, light,'
            . ' dark. Can you use one of them?';

        yield[
            '{{ meritoo_flash_message_render_messages({
                \'test1\': \'Test 1\',
                \'test2\': \'Test 2\'
            }) }}',
            sprintf($messageTemplate, 'test1'),
        ];

        yield[
            '{{ meritoo_flash_message_render_messages({
                \'success\': \'Saved\',
                \'test2\': \'Test 2\'
            }) }}',
            sprintf($messageTemplate, 'test2'),
        ];

        yield[
            '{{ meritoo_flash_message_render_messages({
                \'test\': \'Test Test Test\'
            }) }}',
            sprintf($messageTemplate, 'test'),
        ];
    }

    /**
     * Provide template to render messages using test environment
     *
     * @return \Generator
     */
    public function provideTemplateToRenderMessagesUsingTestEnvironment(): \Generator
    {
        $containerTemplate = '<div class="all-flash-messages">%s</div>';
        $messageTemplate = '<div class="message %s-message-type single-row" role="alert">%s</div>';

        yield[
            '{{ meritoo_flash_message_render_messages({
                \'positive\': \'Data saved\',
            }) }}',
            sprintf($containerTemplate, sprintf($messageTemplate, 'positive', 'Data saved')),
        ];

        yield[
            '{{ meritoo_flash_message_render_messages({
                \'negative\': \'Oops, not saved\',
                \'information\': \'Check connection\'
            }) }}',
            sprintf(
                $containerTemplate,
                sprintf($messageTemplate, 'negative', 'Oops, not saved')
                . sprintf($messageTemplate, 'information', 'Check connection')
            ),
        ];

        yield[
            '{{ meritoo_flash_message_render_messages({
                \'positive\': [
                    \'Email is valid\',
                    \'Phone is valid\'
                ],
                \'information\': \'Did you see that?\'
            }) }}',
            sprintf(
                $containerTemplate,
                sprintf($messageTemplate, 'positive', 'Email is valid')
                . sprintf($messageTemplate, 'positive', 'Phone is valid')
                . sprintf($messageTemplate, 'information', 'Did you see that?')
            ),
        ];

        yield[
            '{{ meritoo_flash_message_render_messages({
                \'positive\': [
                    \'Email is valid\',
                    \'Phone is valid\'
                ],
                \'information\': \'Did you see that?\',
                \'negative\': [
                    \'Price cannot be negative\'
                ]
            }) }}',
            sprintf(
                $containerTemplate,
                sprintf($messageTemplate, 'positive', 'Email is valid')
                . sprintf($messageTemplate, 'positive', 'Phone is valid')
                . sprintf($messageTemplate, 'information', 'Did you see that?')
                . sprintf($messageTemplate, 'negative', 'Price cannot be negative')
            ),
        ];
    }

    /**
     * Provide template to render messages using defaults
     *
     * @return \Generator
     */
    public function provideTemplateToRenderMessagesUsingDefaults(): \Generator
    {
        $containerTemplate = '<div class="alerts">%s</div>';
        $messageTemplate = '<div class="alert alert-%s" role="alert">%s</div>';

        yield[
            '{{ meritoo_flash_message_render_messages({
                \'success\': \'Data saved\',
            }) }}',
            sprintf($containerTemplate, sprintf($messageTemplate, 'success', 'Data saved')),
        ];

        yield[
            '{{ meritoo_flash_message_render_messages({
                \'danger\': \'Oops, not saved\',
                \'info\': \'Check connection\'
            }) }}',
            sprintf(
                $containerTemplate,
                sprintf($messageTemplate, 'danger', 'Oops, not saved')
                . sprintf($messageTemplate, 'info', 'Check connection')
            ),
        ];

        yield[
            '{{ meritoo_flash_message_render_messages({
                \'success\': [
                    \'Email is valid\',
                    \'Phone is valid\'
                ],
                \'info\': \'Did you see that?\'
            }) }}',
            sprintf(
                $containerTemplate,
                sprintf($messageTemplate, 'success', 'Email is valid')
                . sprintf($messageTemplate, 'success', 'Phone is valid')
                . sprintf($messageTemplate, 'info', 'Did you see that?')
            ),
        ];

        yield[
            '{{ meritoo_flash_message_render_messages({
                \'success\': [
                    \'Email is valid\',
                    \'Phone is valid\'
                ],
                \'info\': \'Did you see that?\',
                \'warning\': [
                    \'Price cannot be negative\'
                ]
            }) }}',
            sprintf(
                $containerTemplate,
                sprintf($messageTemplate, 'success', 'Email is valid')
                . sprintf($messageTemplate, 'success', 'Phone is valid')
                . sprintf($messageTemplate, 'info', 'Did you see that?')
                . sprintf($messageTemplate, 'warning', 'Price cannot be negative')
            ),
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getExtensionNamespace(): string
    {
        return FlashExtension::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();
        static::bootKernel();
    }
}
