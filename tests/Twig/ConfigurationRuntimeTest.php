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
use Meritoo\FlashBundle\Exception\UnavailableFlashMessageTypeException;
use Meritoo\FlashBundle\Twig\ConfigurationRuntime;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Test case for the runtime class related to FlashExtension Twig Extension
 *
 * @author    Meritoo <github@meritoo.pl>
 * @copyright Meritoo
 *
 * @internal
 * @coversNothing
 */
class ConfigurationRuntimeTest extends KernelTestCase
{
    use BaseTestCaseTrait;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();
        static::bootKernel();
    }

    /**
     * @covers \Meritoo\FlashBundle\Twig\ConfigurationRuntime::__construct
     */
    public function testConstructor(): void
    {
        static::assertConstructorVisibilityAndArguments(
            ConfigurationRuntime::class,
            OopVisibilityType::IS_PUBLIC,
            6,
            6
        );
    }

    /**
     * @covers \Meritoo\FlashBundle\Twig\ConfigurationRuntime::getContainerCssClasses
     */
    public function testGetContainerCssClassesUsingTestEnvironment(): void
    {
        $containerCssClasses = static::$container
            ->get(ConfigurationRuntime::class)
            ->getContainerCssClasses()
        ;

        static::assertSame('all-flash-messages', $containerCssClasses);
    }

    /**
     * @covers \Meritoo\FlashBundle\Twig\ConfigurationRuntime::getContainerCssClasses
     */
    public function testGetContainerCssClassesUsingDefaults(): void
    {
        static::bootKernel([
            'environment' => 'defaults',
        ]);

        $containerCssClasses = static::$container
            ->get(ConfigurationRuntime::class)
            ->getContainerCssClasses()
        ;

        static::assertSame('alerts', $containerCssClasses);
    }

    /**
     * @param string $unavailableFlashMessageType Unavailable type of flash message
     *
     * @dataProvider provideUnavailableFlashMessageType
     * @covers       \Meritoo\FlashBundle\Twig\ConfigurationRuntime::getOneFlashMessageCssClasses
     */
    public function testGetOneFlashMessageCssClassesUsingTestEnvironmentAndUnavailableFlashMessageType(
        string $unavailableFlashMessageType
    ): void {
        $this->expectException(UnavailableFlashMessageTypeException::class);

        static::$container
            ->get(ConfigurationRuntime::class)
            ->getOneFlashMessageCssClasses($unavailableFlashMessageType)
        ;
    }

    /**
     * @param string $unavailableFlashMessageType Unavailable type of flash message
     *
     * @dataProvider provideUnavailableFlashMessageType
     * @covers       \Meritoo\FlashBundle\Twig\ConfigurationRuntime::getOneFlashMessageCssClasses
     */
    public function testGetOneFlashMessageCssClassesUsingDefaultsAndUnavailableFlashMessageType(
        string $unavailableFlashMessageType
    ): void {
        $this->expectException(UnavailableFlashMessageTypeException::class);

        static::bootKernel([
            'environment' => 'defaults',
        ]);

        static::$container
            ->get(ConfigurationRuntime::class)
            ->getOneFlashMessageCssClasses($unavailableFlashMessageType)
        ;
    }

    /**
     * @param string $flashMessageType Type of flash message
     * @param string $expected         Expected CSS classes for one flash message
     *
     * @dataProvider provideFlashMessageTypeUsingTestEnvironment
     * @covers       \Meritoo\FlashBundle\Twig\ConfigurationRuntime::getOneFlashMessageCssClasses
     */
    public function testGetOneFlashMessageCssClassesUsingTestEnvironment(
        string $flashMessageType,
        string $expected
    ): void {
        $cssClasses = static::$container
            ->get(ConfigurationRuntime::class)
            ->getOneFlashMessageCssClasses($flashMessageType)
        ;

        static::assertSame($expected, $cssClasses);
    }

    /**
     * @param string $flashMessageType Type of flash message
     * @param string $expected         Expected CSS classes for one flash message
     *
     * @dataProvider provideFlashMessageTypeUsingDefaults
     * @covers       \Meritoo\FlashBundle\Twig\ConfigurationRuntime::getOneFlashMessageCssClasses
     */
    public function testGetOneFlashMessageCssClassesUsingDefaults(string $flashMessageType, string $expected): void
    {
        static::bootKernel([
            'environment' => 'defaults',
        ]);

        $cssClasses = static::$container
            ->get(ConfigurationRuntime::class)
            ->getOneFlashMessageCssClasses($flashMessageType)
        ;

        static::assertSame($expected, $cssClasses);
    }

    /**
     * @param string $flashMessageType Type of flash message
     * @param string $expected         Expected CSS classes for one flash message
     *
     * @dataProvider provideFlashMessageTypeUsingCssWithoutPlaceholder
     * @covers       \Meritoo\FlashBundle\Twig\ConfigurationRuntime::getOneFlashMessageCssClasses
     */
    public function testGetOneFlashMessageCssClassesWithoutPlaceholder(string $flashMessageType, string $expected): void
    {
        static::bootKernel([
            'environment' => 'css_without_placeholder',
        ]);

        $cssClasses = static::$container
            ->get(ConfigurationRuntime::class)
            ->getOneFlashMessageCssClasses($flashMessageType)
        ;

        static::assertSame($expected, $cssClasses);
    }

    /**
     * Provide unavailable type of flash message
     *
     * @return \Generator
     */
    public function provideUnavailableFlashMessageType(): \Generator
    {
        yield[
            '',
        ];

        yield[
            'test',
        ];

        yield[
            'lorem ipsum',
        ];

        yield[
            '1234',
        ];
    }

    /**
     * Provide type of flash message using test environment
     *
     * @return \Generator
     */
    public function provideFlashMessageTypeUsingTestEnvironment(): \Generator
    {
        yield[
            'positive',
            'message positive-message-type single-row',
        ];

        yield[
            'negative',
            'message negative-message-type single-row',
        ];

        yield[
            'information',
            'message information-message-type single-row',
        ];
    }

    /**
     * Provide type of flash message using default configuration
     *
     * @return \Generator
     */
    public function provideFlashMessageTypeUsingDefaults(): \Generator
    {
        yield[
            'primary',
            'alert alert-primary',
        ];

        yield[
            'secondary',
            'alert alert-secondary',
        ];

        yield[
            'success',
            'alert alert-success',
        ];

        yield[
            'info',
            'alert alert-info',
        ];

        yield[
            'warning',
            'alert alert-warning',
        ];

        yield[
            'danger',
            'alert alert-danger',
        ];

        yield[
            'light',
            'alert alert-light',
        ];

        yield[
            'dark',
            'alert alert-dark',
        ];
    }

    /**
     * Provide flash message type using css without placeholder
     *
     * @return \Generator
     */
    public function provideFlashMessageTypeUsingCssWithoutPlaceholder(): \Generator
    {
        yield[
            'success',
            'message message-type single-row',
        ];

        yield[
            'danger',
            'message message-type single-row',
        ];

        yield[
            'info',
            'message message-type single-row',
        ];
    }
}
