<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Meritoo\Test\FlashBundle\Twig;

use Meritoo\Common\Traits\Test\Base\BaseTestCaseTrait;
use Meritoo\Common\Type\OopVisibilityType;
use Meritoo\Common\Utilities\Bundle;
use Meritoo\Common\Utilities\Reflection;
use Meritoo\FlashBundle\Exception\UnavailableFlashMessageTypeException;
use Meritoo\FlashBundle\MeritooFlashBundle;
use Meritoo\FlashBundle\Service\FlashMessageService;
use Meritoo\FlashBundle\Twig\FlashRuntime;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Templating\EngineInterface;

/**
 * Test case for the runtime class related to FlashExtension Twig Extension
 *
 * @author    Meritoo <github@meritoo.pl>
 * @copyright Meritoo
 */
class FlashRuntimeTest extends KernelTestCase
{
    use BaseTestCaseTrait;

    /**
     * @covers \Meritoo\FlashBundle\Twig\FlashRuntime::__construct
     */
    public function testConstructor(): void
    {
        static::assertConstructorVisibilityAndArguments(
            FlashRuntime::class,
            OopVisibilityType::IS_PUBLIC,
            5,
            5
        );
    }

    /**
     * @param array $messages Flash messages
     *
     * @dataProvider provideFlashMessagesUsingTestEnvironmentAndUnavailableFlashMessageType
     * @covers       \Meritoo\FlashBundle\Twig\FlashRuntime::renderFlashMessagesFromSession
     */
    public function testRenderFlashMessagesFromSessionUsingTestEnvironmentAndUnavailableFlashMessageType(
        array $messages
    ): void {
        $this->expectException(UnavailableFlashMessageTypeException::class);

        $this
            ->getFlashRuntime($messages)
            ->renderFlashMessagesFromSession();
    }

    /**
     * @param array $messages Flash messages
     *
     * @dataProvider provideFlashMessagesUsingTestEnvironmentAndUnavailableFlashMessageType
     * @covers       \Meritoo\FlashBundle\Twig\FlashRuntime::renderFlashMessagesFromSession
     */
    public function testRenderFlashMessagesFromSessionUsingDefaultsAndUnavailableFlashMessageType(
        array $messages
    ): void {
        $this->expectException(UnavailableFlashMessageTypeException::class);

        static::bootKernel([
            'environment' => 'defaults',
        ]);

        $this
            ->getFlashRuntime($messages)
            ->renderFlashMessagesFromSession();
    }

    /**
     * @param array  $messages Flash messages
     * @param string $expected Expected result of rendering
     *
     * @dataProvider provideFlashMessagesUsingTestEnvironment
     * @covers       \Meritoo\FlashBundle\Twig\FlashRuntime::renderFlashMessagesFromSession
     */
    public function testRenderFlashMessagesFromSessionUsingTestEnvironment(array $messages, string $expected): void
    {
        $rendered = $this
            ->getFlashRuntime($messages)
            ->renderFlashMessagesFromSession();

        static::assertSame($expected, $rendered);
    }

    /**
     * @param array  $messages Flash messages
     * @param string $expected Expected result of rendering
     *
     * @dataProvider provideFlashMessagesUsingDefaults
     * @covers       \Meritoo\FlashBundle\Twig\FlashRuntime::renderFlashMessagesFromSession
     */
    public function testRenderFlashMessagesFromSessionUsingDefaults(array $messages, string $expected): void
    {
        static::bootKernel([
            'environment' => 'defaults',
        ]);

        $rendered = $this
            ->getFlashRuntime($messages)
            ->renderFlashMessagesFromSession();

        static::assertSame($expected, $rendered);
    }

    /**
     * @param array $messages Flash messages to render. Key-value pairs:
     *                        - key - type of flash message
     *                        - value - flash message
     *
     * @dataProvider provideFlashMessagesUsingTestEnvironmentAndUnavailableFlashMessageType
     * @covers       \Meritoo\FlashBundle\Twig\FlashRuntime::renderFlashMessages
     */
    public function testRenderFlashMessagesUsingTestEnvironmentAndUnavailableFlashMessageType(array $messages): void
    {
        $this->expectException(UnavailableFlashMessageTypeException::class);

        static::$container
            ->get(FlashRuntime::class)
            ->renderFlashMessages($messages);
    }

    /**
     * @param array $messages Flash messages to render. Key-value pairs:
     *                        - key - type of flash message
     *                        - value - flash message
     *
     * @dataProvider provideFlashMessagesUsingTestEnvironmentAndUnavailableFlashMessageType
     * @covers       \Meritoo\FlashBundle\Twig\FlashRuntime::renderFlashMessages
     */
    public function testRenderFlashMessagesUsingDefaultsAndUnavailableFlashMessageType(array $messages): void
    {
        $this->expectException(UnavailableFlashMessageTypeException::class);

        static::bootKernel([
            'environment' => 'defaults',
        ]);

        static::$container
            ->get(FlashRuntime::class)
            ->renderFlashMessages($messages);
    }

    /**
     * @param array  $messages Flash messages to render. Key-value pairs:
     *                         - key - type of flash message
     *                         - value - flash message
     * @param string $expected Expected result of rendering
     *
     * @dataProvider provideFlashMessagesUsingTestEnvironment
     * @covers       \Meritoo\FlashBundle\Twig\FlashRuntime::renderFlashMessages
     */
    public function testRenderFlashMessagesUsingTestEnvironment(array $messages, string $expected): void
    {
        $rendered = static::$container
            ->get(FlashRuntime::class)
            ->renderFlashMessages($messages);

        static::assertSame($expected, $rendered);
    }

    /**
     * @param array  $messages Flash messages to render. Key-value pairs:
     *                         - key - type of flash message
     *                         - value - flash message
     * @param string $expected Expected result of rendering
     *
     * @dataProvider provideFlashMessagesUsingDefaults
     * @covers       \Meritoo\FlashBundle\Twig\FlashRuntime::renderFlashMessages
     */
    public function testRenderFlashMessagesUsingDefaults(array $messages, string $expected): void
    {
        static::bootKernel([
            'environment' => 'defaults',
        ]);

        $rendered = static::$container
            ->get(FlashRuntime::class)
            ->renderFlashMessages($messages);

        static::assertSame($expected, $rendered);
    }

    /**
     * @param array $messages Flash messages to add
     * @param bool  $expected Expected information if there are any flash messages
     *
     * @dataProvider provideFlashMessagesToVerifyExistenceUsingTestEnvironment
     * @covers       \Meritoo\FlashBundle\Twig\FlashRuntime::hasFlashMessages
     */
    public function testHasFlashMessagesUsingTestEnvironment(array $messages, bool $expected): void
    {
        static::assertSame($expected, $this->getFlashRuntime($messages)->hasFlashMessages());
    }

    /**
     * @param array $messages Flash messages to add
     * @param bool  $expected Expected information if there are any flash messages
     *
     * @dataProvider provideFlashMessagesToVerifyExistenceUsingDefaults
     * @covers       \Meritoo\FlashBundle\Twig\FlashRuntime::hasFlashMessages
     */
    public function testHasFlashMessagesUsingDefaults(array $messages, bool $expected): void
    {
        static::bootKernel([
            'environment' => 'defaults',
        ]);

        static::assertSame($expected, $this->getFlashRuntime($messages)->hasFlashMessages());
    }

    /**
     * Provide flash messages using test environment and unavailable flash message type
     *
     * @return \Generator
     */
    public function provideFlashMessagesUsingTestEnvironmentAndUnavailableFlashMessageType(): \Generator
    {
        yield[
            [
                'test1' => 'test 1',
                'test2' => 'test 2',
            ],
        ];

        yield[
            [
                'prime' => 'test',
            ],
        ];

        yield[
            [
                'green' => 'test',
            ],
        ];
    }

    /**
     * Provide flash messages for session using test environment
     *
     * @return \Generator
     */
    public function provideFlashMessagesUsingTestEnvironment(): \Generator
    {
        $containerTemplate = '<div class="all-flash-messages">%s</div>';
        $messageTemplate = '<div class="message %s-message-type single-row" role="alert">%s</div>';

        yield[
            [
                'positive' => 'Data saved',
            ],
            sprintf($containerTemplate, sprintf($messageTemplate, 'positive', 'Data saved')),
        ];

        yield[
            [
                'negative'    => 'Oops, not saved',
                'information' => 'Check connection',
            ],
            sprintf(
                $containerTemplate,
                sprintf($messageTemplate, 'negative', 'Oops, not saved')
                . sprintf($messageTemplate, 'information', 'Check connection')
            ),
        ];

        yield[
            [
                'positive'    => [
                    'Email is valid',
                    'Phone is valid',
                ],
                'information' => 'Did you see that?',
            ],
            sprintf(
                $containerTemplate,
                sprintf($messageTemplate, 'positive', 'Email is valid')
                . sprintf($messageTemplate, 'positive', 'Phone is valid')
                . sprintf($messageTemplate, 'information', 'Did you see that?')
            ),
        ];

        yield[
            [
                'positive'    => [
                    'Email is valid',
                    'Phone is valid',
                ],
                'information' => 'Did you see that?',
                'negative'    => [
                    'Price cannot be negative',
                ],
            ],
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
     * Provide flash messages for session using default configuration
     *
     * @return \Generator
     */
    public function provideFlashMessagesUsingDefaults(): \Generator
    {
        $containerTemplate = '<div class="alerts">%s</div>';
        $messageTemplate = '<div class="alert alert-%s" role="alert">%s</div>';

        yield[
            [
                'success' => 'Data saved',
            ],
            sprintf($containerTemplate, sprintf($messageTemplate, 'success', 'Data saved')),
        ];

        yield[
            [
                'danger' => 'Oops, not saved',
                'info'   => 'Check connection',
            ],
            sprintf(
                $containerTemplate,
                sprintf($messageTemplate, 'danger', 'Oops, not saved')
                . sprintf($messageTemplate, 'info', 'Check connection')
            ),
        ];

        yield[
            [
                'success' => [
                    'Email is valid',
                    'Phone is valid',
                ],
                'info'    => 'Did you see that?',
            ],
            sprintf(
                $containerTemplate,
                sprintf($messageTemplate, 'success', 'Email is valid')
                . sprintf($messageTemplate, 'success', 'Phone is valid')
                . sprintf($messageTemplate, 'info', 'Did you see that?')
            ),
        ];

        yield[
            [
                'success' => [
                    'Email is valid',
                    'Phone is valid',
                ],
                'info'    => 'Did you see that?',
                'danger'  => [
                    'Price cannot be negative',
                ],
            ],
            sprintf(
                $containerTemplate,
                sprintf($messageTemplate, 'success', 'Email is valid')
                . sprintf($messageTemplate, 'success', 'Phone is valid')
                . sprintf($messageTemplate, 'info', 'Did you see that?')
                . sprintf($messageTemplate, 'danger', 'Price cannot be negative')
            ),
        ];
    }

    /**
     * Provide flash messages to verify existence
     *
     * @return \Generator
     */
    public function provideFlashMessagesToVerifyExistenceUsingTestEnvironment(): \Generator
    {
        yield[
            [],
            false,
        ];

        yield[
            [
                'negative' => 'Oops, not saved',
            ],
            true,
        ];

        yield[
            [
                'negative'    => 'Oops, not saved',
                'information' => 'Try again',
            ],
            true,
        ];

        yield[
            [
                'positive'    => [
                    'Saved',
                    'Check your mailbox',
                ],
                'information' => 'You are registered user now',
            ],
            true,
        ];
    }

    /**
     * Provide flash messages to verify existence using default configuration
     *
     * @return \Generator
     */
    public function provideFlashMessagesToVerifyExistenceUsingDefaults(): \Generator
    {
        yield[
            [],
            false,
        ];

        yield[
            [
                'danger' => 'Oops, not saved',
            ],
            true,
        ];

        yield[
            [
                'danger' => 'Oops, not saved',
                'info'   => 'Try again',
            ],
            true,
        ];

        yield[
            [
                'success' => [
                    'Saved',
                    'Check your mailbox',
                ],
                'info'    => 'You are registered user now',
            ],
            true,
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();
        static::bootKernel();
    }

    /**
     * Returns instance of FlashRuntime with all related and mocked instances
     *
     * @param array $messagesForSession Flash messages to add
     * @return FlashRuntime
     */
    private function getFlashRuntime(array $messagesForSession): FlashRuntime
    {
        $request = $this->createMock(Request::class);
        $request->method('hasPreviousSession')->willReturn(true);

        $flashMessageService = static::$container
            ->get(FlashMessageService::class)
            ->addFlashMessages($messagesForSession);

        $requestStack = $this->createMock(RequestStack::class);
        $requestStack->method('getCurrentRequest')->willReturn($request);

        $session = static::$container->get('session');
        /* @var EngineInterface $twigEngine */
        $twigEngine = static::$container->get('templating');

        $bundleName = Reflection::getClassName(MeritooFlashBundle::class, true);
        $manyMessagesTemplatePath = Bundle::getBundleViewPath('many', $bundleName);

        return new FlashRuntime(
            $flashMessageService,
            $requestStack,
            $session,
            $twigEngine,
            $manyMessagesTemplatePath
        );
    }
}
