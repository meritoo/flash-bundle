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

/**
 * Test case for the runtime class related to FlashExtension Twig Extension
 *
 * @author    Meritoo <github@meritoo.pl>
 * @copyright Meritoo
 */
class FlashRuntimeTest extends KernelTestCase
{
    use BaseTestCaseTrait;

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
     * @dataProvider provideFlashMessagesForSessionUsingTestEnvironmentAndUnavailableFlashMessageType
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
     * @dataProvider provideFlashMessagesForSessionUsingTestEnvironmentAndUnavailableFlashMessageType
     */
    public function testRenderFlashMessagesFromSessionUsingDefaultsAndUnavailableFlashMessageType(
        array $messages
    ): void {
        $this->expectException(UnavailableFlashMessageTypeException::class);

        $this
            ->getFlashRuntime($messages)
            ->renderFlashMessagesFromSession();
    }

    /**
     * @param array  $messages Flash messages
     * @param string $expected Expected result of rendering
     *
     * @dataProvider provideFlashMessagesForSessionUsingTestEnvironment
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
     * @dataProvider provideFlashMessagesForSessionUsingDefaults
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
     * Provide flash messages for session using test environment and unavailable flash message type
     *
     * @return \Generator
     */
    public function provideFlashMessagesForSessionUsingTestEnvironmentAndUnavailableFlashMessageType(): \Generator
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
    public function provideFlashMessagesForSessionUsingTestEnvironment(): \Generator
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
    public function provideFlashMessagesForSessionUsingDefaults(): \Generator
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
