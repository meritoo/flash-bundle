<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Meritoo\Test\FlashBundle\Service;

use Meritoo\Common\Traits\Test\Base\BaseTestCaseTrait;
use Meritoo\Common\Type\OopVisibilityType;
use Meritoo\FlashBundle\Exception\UnavailableFlashMessageTypeException;
use Meritoo\FlashBundle\Service\FlashMessageService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Test case for the service related to flash messages
 *
 * @author    Meritoo <github@meritoo.pl>
 * @copyright Meritoo
 *
 * @internal
 * @covers    \Meritoo\FlashBundle\Service\FlashMessageService
 */
class FlashMessageServiceTest extends KernelTestCase
{
    use BaseTestCaseTrait;

    /**
     * @covers \Meritoo\FlashBundle\Service\FlashMessageService::__construct
     */
    public function testConstructor(): void
    {
        static::assertConstructorVisibilityAndArguments(
            FlashMessageService::class,
            OopVisibilityType::IS_PUBLIC,
            5,
            5
        );
    }

    /**
     * @covers \Meritoo\FlashBundle\Service\FlashMessageService::prepareMessages
     */
    public function testPrepareMessagesWithoutFlashMessageTypes(): void
    {
        $messages = [];

        static::$container
            ->get(FlashMessageService::class)
            ->prepareMessages($messages)
        ;

        static::assertSame([], $messages);
    }

    /**
     * @param array $messages Flash messages to verify
     *
     * @dataProvider provideMessagesToPrepareUsingUnavailableFlashMessageType
     * @covers       \Meritoo\FlashBundle\Service\FlashMessageService::prepareMessages
     */
    public function testPrepareMessagesUsingTestEnvironmentAndUnavailableFlashMessageType(array $messages): void
    {
        $this->expectException(UnavailableFlashMessageTypeException::class);

        static::$container
            ->get(FlashMessageService::class)
            ->prepareMessages($messages)
        ;
    }

    /**
     * @param array $messages Flash messages to verify
     *
     * @dataProvider provideMessagesToPrepareUsingUnavailableFlashMessageType
     * @covers       \Meritoo\FlashBundle\Service\FlashMessageService::prepareMessages
     */
    public function testPrepareMessagesUsingDefaultsAndUnavailableFlashMessageType(array $messages): void
    {
        $this->expectException(UnavailableFlashMessageTypeException::class);

        static::bootKernel([
            'environment' => 'defaults',
        ]);

        static::$container
            ->get(FlashMessageService::class)
            ->prepareMessages($messages)
        ;
    }

    /**
     * @param array $messages         Flash messages to verify
     * @param array $expectedMessages Expected flash messages after verification
     *
     * @dataProvider provideMessagesToPrepareUsingTestEnvironment
     * @covers       \Meritoo\FlashBundle\Service\FlashMessageService::prepareMessages
     */
    public function testPrepareMessagesUsingTestEnvironment(array $messages, array $expectedMessages): void
    {
        static::$container
            ->get(FlashMessageService::class)
            ->prepareMessages($messages)
        ;

        static::assertSame($expectedMessages, $messages);
    }

    /**
     * @param array $messages         Flash messages to verify
     * @param array $expectedMessages Expected flash messages after verification
     *
     * @dataProvider provideMessagesToPrepareUsingDefaults
     * @covers       \Meritoo\FlashBundle\Service\FlashMessageService::prepareMessages
     */
    public function testPrepareMessagesUsingDefaults(array $messages, array $expectedMessages): void
    {
        static::bootKernel([
            'environment' => 'defaults',
        ]);

        static::$container
            ->get(FlashMessageService::class)
            ->prepareMessages($messages)
        ;

        static::assertSame($expectedMessages, $messages);
    }

    /**
     * @param string $flashMessageType Type of flash message to verify, e.g. "warning"
     *
     * @dataProvider provideUnavailableFlashMessageTypeToVerify
     * @covers       \Meritoo\FlashBundle\Service\FlashMessageService::verifyFlashMessageType
     */
    public function testVerifyFlashMessageTypeUsingTestEnvironmentAndUnavailableFlashMessageType(
        string $flashMessageType
    ): void {
        $this->expectException(UnavailableFlashMessageTypeException::class);

        static::$container
            ->get(FlashMessageService::class)
            ->verifyFlashMessageType($flashMessageType)
        ;
    }

    /**
     * @param string $flashMessageType Type of flash message to verify, e.g. "warning"
     *
     * @dataProvider provideUnavailableFlashMessageTypeToVerify
     * @covers       \Meritoo\FlashBundle\Service\FlashMessageService::verifyFlashMessageType
     */
    public function testVerifyFlashMessageTypeUsingDefaultsAndUnavailableFlashMessageType(
        string $flashMessageType
    ): void {
        $this->expectException(UnavailableFlashMessageTypeException::class);

        static::bootKernel([
            'environment' => 'defaults',
        ]);

        static::$container
            ->get(FlashMessageService::class)
            ->verifyFlashMessageType($flashMessageType)
        ;
    }

    /**
     * @param string $flashMessageType Type of flash message to verify, e.g. "warning"
     *
     * @dataProvider provideFlashMessageTypeToVerifyUsingTestEnvironment
     * @covers       \Meritoo\FlashBundle\Service\FlashMessageService::verifyFlashMessageType
     */
    public function testVerifyFlashMessageTypeUsingTestEnvironment(string $flashMessageType): void
    {
        $result = static::$container
            ->get(FlashMessageService::class)
            ->verifyFlashMessageType($flashMessageType)
        ;

        static::assertInstanceOf(FlashMessageService::class, $result);
    }

    /**
     * @param string $flashMessageType Type of flash message to verify, e.g. "warning"
     *
     * @dataProvider provideFlashMessageTypeToVerifyUsingDefaults
     * @covers       \Meritoo\FlashBundle\Service\FlashMessageService::verifyFlashMessageType
     */
    public function testVerifyFlashMessageTypeUsingDefaults(string $flashMessageType): void
    {
        static::bootKernel([
            'environment' => 'defaults',
        ]);

        $result = static::$container
            ->get(FlashMessageService::class)
            ->verifyFlashMessageType($flashMessageType)
        ;

        static::assertInstanceOf(FlashMessageService::class, $result);
    }

    /**
     * @covers \Meritoo\FlashBundle\Service\FlashMessageService::verifyFlashMessageTypes
     */
    public function testVerifyFlashMessageTypesWithoutFlashMessageTypes(): void
    {
        $result = static::$container
            ->get(FlashMessageService::class)
            ->verifyFlashMessageTypes([])
        ;

        static::assertInstanceOf(FlashMessageService::class, $result);
    }

    /**
     * @param array $flashMessageTypes Types of flash message to verify, e.g. ["warning", "success"]
     *
     * @dataProvider provideUnavailableFlashMessageTypesToVerify
     * @covers       \Meritoo\FlashBundle\Service\FlashMessageService::verifyFlashMessageTypes
     */
    public function testVerifyFlashMessageTypesUsingTestEnvironmentAndUnavailableFlashMessageType(
        array $flashMessageTypes
    ): void {
        $this->expectException(UnavailableFlashMessageTypeException::class);

        static::$container
            ->get(FlashMessageService::class)
            ->verifyFlashMessageTypes($flashMessageTypes)
        ;
    }

    /**
     * @param array $flashMessageTypes Types of flash message to verify, e.g. ["warning", "success"]
     *
     * @dataProvider provideUnavailableFlashMessageTypesToVerify
     * @covers       \Meritoo\FlashBundle\Service\FlashMessageService::verifyFlashMessageTypes
     */
    public function testVerifyFlashMessageTypesUsingDefaultsAndUnavailableFlashMessageType(
        array $flashMessageTypes
    ): void {
        $this->expectException(UnavailableFlashMessageTypeException::class);

        static::bootKernel([
            'environment' => 'defaults',
        ]);

        static::$container
            ->get(FlashMessageService::class)
            ->verifyFlashMessageTypes($flashMessageTypes)
        ;
    }

    /**
     * @param array $flashMessageTypes Types of flash message to verify, e.g. ["warning", "success"]
     *
     * @dataProvider provideFlashMessageTypesToVerifyUsingTestEnvironment
     * @covers       \Meritoo\FlashBundle\Service\FlashMessageService::verifyFlashMessageTypes
     */
    public function testVerifyFlashMessageTypesUsingTestEnvironment(array $flashMessageTypes): void
    {
        $result = static::$container
            ->get(FlashMessageService::class)
            ->verifyFlashMessageTypes($flashMessageTypes)
        ;

        static::assertInstanceOf(FlashMessageService::class, $result);
    }

    /**
     * @param array $flashMessageTypes Types of flash message to verify, e.g. ["warning", "success"]
     *
     * @dataProvider provideFlashMessageTypesToVerifyUsingDefaults
     * @covers       \Meritoo\FlashBundle\Service\FlashMessageService::verifyFlashMessageTypes
     */
    public function testVerifyFlashMessageTypesUsingDefaults(array $flashMessageTypes): void
    {
        static::bootKernel([
            'environment' => 'defaults',
        ]);

        $result = static::$container
            ->get(FlashMessageService::class)
            ->verifyFlashMessageTypes($flashMessageTypes)
        ;

        static::assertInstanceOf(FlashMessageService::class, $result);
    }

    /**
     * @covers \Meritoo\FlashBundle\Service\FlashMessageService::addFlashMessages
     */
    public function testAddFlashMessagesWithoutFlashMessages(): void
    {
        $result = static::$container
            ->get(FlashMessageService::class)
            ->addFlashMessages([])
        ;

        $allFlashMessages = static::$container
            ->get(SessionInterface::class)
            ->getFlashBag()
            ->all()
        ;

        static::assertInstanceOf(FlashMessageService::class, $result);
        static::assertCount(0, $allFlashMessages);
    }

    /**
     * @param array $messages Flash messages to add
     *
     * @dataProvider provideFlashMessagesToAddUsingUnavailableFlashMessageType
     * @covers       \Meritoo\FlashBundle\Service\FlashMessageService::addFlashMessages
     */
    public function testAddFlashMessagesUsingUnavailableFlashMessageTypeAndTestEnvironment(
        array $messages
    ): void {
        $this->expectException(UnavailableFlashMessageTypeException::class);

        static::$container
            ->get(FlashMessageService::class)
            ->addFlashMessages($messages)
        ;
    }

    /**
     * @param array $messages Flash messages to add
     *
     * @dataProvider provideFlashMessagesToAddUsingUnavailableFlashMessageType
     * @covers       \Meritoo\FlashBundle\Service\FlashMessageService::addFlashMessages
     */
    public function testAddFlashMessagesUsingUnavailableFlashMessageTypeAndDefaults(
        array $messages
    ): void {
        $this->expectException(UnavailableFlashMessageTypeException::class);

        static::bootKernel([
            'environment' => 'defaults',
        ]);

        static::$container
            ->get(FlashMessageService::class)
            ->addFlashMessages($messages)
        ;
    }

    /**
     * @param array $messages         Flash messages to add
     * @param array $expectedMessages Expected flash messages
     *
     * @dataProvider provideFlashMessagesToAddUsingTestEnvironment
     * @covers       \Meritoo\FlashBundle\Service\FlashMessageService::addFlashMessages
     */
    public function testAddFlashMessagesUsingTestEnvironment(array $messages, array $expectedMessages): void
    {
        $result = static::$container
            ->get(FlashMessageService::class)
            ->addFlashMessages($messages)
        ;

        $allFlashMessages = static::$container
            ->get(SessionInterface::class)
            ->getFlashBag()
            ->all()
        ;

        static::assertInstanceOf(FlashMessageService::class, $result);
        static::assertSame($expectedMessages, $allFlashMessages);
    }

    /**
     * @param array $messages         Flash messages to add
     * @param array $expectedMessages Expected flash messages
     *
     * @dataProvider provideFlashMessagesToAddUsingDefaults
     * @covers       \Meritoo\FlashBundle\Service\FlashMessageService::addFlashMessages
     */
    public function testAddFlashMessagesUsingDefaults(array $messages, array $expectedMessages): void
    {
        static::bootKernel([
            'environment' => 'defaults',
        ]);

        $result = static::$container
            ->get(FlashMessageService::class)
            ->addFlashMessages($messages)
        ;

        $allFlashMessages = static::$container
            ->get(SessionInterface::class)
            ->getFlashBag()
            ->all()
        ;

        static::assertInstanceOf(FlashMessageService::class, $result);
        static::assertSame($expectedMessages, $allFlashMessages);
    }

    /**
     * @covers \Meritoo\FlashBundle\Service\FlashMessageService::addPositiveFlashMessages
     */
    public function testAddPositiveFlashMessagesWithoutFlashMessages(): void
    {
        $result = static::$container
            ->get(FlashMessageService::class)
            ->addPositiveFlashMessages([])
        ;

        $allFlashMessages = static::$container
            ->get(SessionInterface::class)
            ->getFlashBag()
            ->all()
        ;

        static::assertInstanceOf(FlashMessageService::class, $result);
        static::assertCount(0, $allFlashMessages);
    }

    /**
     * @param array $messages         Flash messages to add
     * @param array $expectedMessages Expected flash messages
     *
     * @dataProvider providePositiveFlashMessagesToAddUsingTestEnvironment
     * @covers       \Meritoo\FlashBundle\Service\FlashMessageService::addPositiveFlashMessages
     */
    public function testAddPositiveFlashMessagesUsingTestEnvironment(array $messages, array $expectedMessages): void
    {
        $result = static::$container
            ->get(FlashMessageService::class)
            ->addPositiveFlashMessages($messages)
        ;

        $allFlashMessages = static::$container
            ->get(SessionInterface::class)
            ->getFlashBag()
            ->all()
        ;

        static::assertInstanceOf(FlashMessageService::class, $result);
        static::assertSame($expectedMessages, $allFlashMessages);
    }

    /**
     * @param array $messages         Flash messages to add
     * @param array $expectedMessages Expected flash messages
     *
     * @dataProvider providePositiveFlashMessagesToAddUsingDefaults
     * @covers       \Meritoo\FlashBundle\Service\FlashMessageService::addPositiveFlashMessages
     */
    public function testAddPositiveFlashMessagesUsingDefaults(array $messages, array $expectedMessages): void
    {
        static::bootKernel([
            'environment' => 'defaults',
        ]);

        $result = static::$container
            ->get(FlashMessageService::class)
            ->addPositiveFlashMessages($messages)
        ;

        $allFlashMessages = static::$container
            ->get(SessionInterface::class)
            ->getFlashBag()
            ->all()
        ;

        static::assertInstanceOf(FlashMessageService::class, $result);
        static::assertSame($expectedMessages, $allFlashMessages);
    }

    /**
     * @covers \Meritoo\FlashBundle\Service\FlashMessageService::addNegativeFlashMessages
     */
    public function testAddNegativeFlashMessagesWithoutFlashMessages(): void
    {
        $result = static::$container
            ->get(FlashMessageService::class)
            ->addNegativeFlashMessages([])
        ;

        $allFlashMessages = static::$container
            ->get(SessionInterface::class)
            ->getFlashBag()
            ->all()
        ;

        static::assertInstanceOf(FlashMessageService::class, $result);
        static::assertCount(0, $allFlashMessages);
    }

    /**
     * @param array $messages         Flash messages to add
     * @param array $expectedMessages Expected flash messages
     *
     * @dataProvider provideNegativeFlashMessagesToAddUsingTestEnvironment
     * @covers       \Meritoo\FlashBundle\Service\FlashMessageService::addNegativeFlashMessages
     */
    public function testAddNegativeFlashMessagesUsingTestEnvironment(array $messages, array $expectedMessages): void
    {
        $result = static::$container
            ->get(FlashMessageService::class)
            ->addNegativeFlashMessages($messages)
        ;

        $allFlashMessages = static::$container
            ->get(SessionInterface::class)
            ->getFlashBag()
            ->all()
        ;

        static::assertInstanceOf(FlashMessageService::class, $result);
        static::assertSame($expectedMessages, $allFlashMessages);
    }

    /**
     * @param array $messages         Flash messages to add
     * @param array $expectedMessages Expected flash messages
     *
     * @dataProvider provideNegativeFlashMessagesToAddUsingDefaults
     * @covers       \Meritoo\FlashBundle\Service\FlashMessageService::addNegativeFlashMessages
     */
    public function testAddNegativeFlashMessagesUsingDefaults(array $messages, array $expectedMessages): void
    {
        static::bootKernel([
            'environment' => 'defaults',
        ]);

        $result = static::$container
            ->get(FlashMessageService::class)
            ->addNegativeFlashMessages($messages)
        ;

        $allFlashMessages = static::$container
            ->get(SessionInterface::class)
            ->getFlashBag()
            ->all()
        ;

        static::assertInstanceOf(FlashMessageService::class, $result);
        static::assertSame($expectedMessages, $allFlashMessages);
    }

    /**
     * @covers \Meritoo\FlashBundle\Service\FlashMessageService::addNeutralFlashMessages
     */
    public function testAddNeutralFlashMessagesWithoutFlashMessages(): void
    {
        $result = static::$container
            ->get(FlashMessageService::class)
            ->addNeutralFlashMessages([])
        ;

        $allFlashMessages = static::$container
            ->get(SessionInterface::class)
            ->getFlashBag()
            ->all()
        ;

        static::assertInstanceOf(FlashMessageService::class, $result);
        static::assertCount(0, $allFlashMessages);
    }

    /**
     * @param array $messages         Flash messages to add
     * @param array $expectedMessages Expected flash messages
     *
     * @dataProvider provideNeutralFlashMessagesToAddUsingTestEnvironment
     * @covers       \Meritoo\FlashBundle\Service\FlashMessageService::addNeutralFlashMessages
     */
    public function testAddNeutralFlashMessagesUsingTestEnvironment(array $messages, array $expectedMessages): void
    {
        $result = static::$container
            ->get(FlashMessageService::class)
            ->addNeutralFlashMessages($messages)
        ;

        $allFlashMessages = static::$container
            ->get(SessionInterface::class)
            ->getFlashBag()
            ->all()
        ;

        static::assertInstanceOf(FlashMessageService::class, $result);
        static::assertSame($expectedMessages, $allFlashMessages);
    }

    /**
     * @param array $messages         Flash messages to add
     * @param array $expectedMessages Expected flash messages
     *
     * @dataProvider provideNeutralFlashMessagesToAddUsingDefaults
     * @covers       \Meritoo\FlashBundle\Service\FlashMessageService::addNeutralFlashMessages
     */
    public function testAddNeutralFlashMessagesUsingDefaults(array $messages, array $expectedMessages): void
    {
        static::bootKernel([
            'environment' => 'defaults',
        ]);

        $result = static::$container
            ->get(FlashMessageService::class)
            ->addNeutralFlashMessages($messages)
        ;

        $allFlashMessages = static::$container
            ->get(SessionInterface::class)
            ->getFlashBag()
            ->all()
        ;

        static::assertInstanceOf(FlashMessageService::class, $result);
        static::assertSame($expectedMessages, $allFlashMessages);
    }

    /**
     * @param array $messages Flash messages stored in session
     * @param bool  $expected Expected information if there are any flash messages
     *
     * @dataProvider provideFlashMessagesToVerifyIfThereAreMessages
     * @covers       \Meritoo\FlashBundle\Service\FlashMessageService::hasFlashMessages
     */
    public function testHasFlashMessages(array $messages, bool $expected): void
    {
        $flashBag = static::$container
            ->get(SessionInterface::class)
            ->getFlashBag()
        ;

        foreach ($messages as $type => $message) {
            $flashBag->add($type, $message);
        }

        $has = static::$container
            ->get(FlashMessageService::class)
            ->hasFlashMessages()
        ;

        static::assertSame($has, $expected);
    }

    /**
     * Provide flash message type to verify using test environment
     *
     * @return \Generator
     */
    public function provideFlashMessageTypeToVerifyUsingTestEnvironment(): \Generator
    {
        yield[
            'positive',
        ];

        yield[
            'negative',
        ];

        yield[
            'information',
        ];
    }

    /**
     * Provide flash message type to verify using default configuration
     *
     * @return \Generator
     */
    public function provideFlashMessageTypeToVerifyUsingDefaults(): \Generator
    {
        yield[
            'primary',
        ];

        yield[
            'secondary',
        ];

        yield[
            'success',
        ];

        yield[
            'info',
        ];

        yield[
            'warning',
        ];

        yield[
            'danger',
        ];

        yield[
            'light',
        ];

        yield[
            'dark',
        ];
    }

    /**
     * Provide unavailable flash message type to verify using defaults
     *
     * @return \Generator
     */
    public function provideUnavailableFlashMessageTypeToVerify(): \Generator
    {
        yield[
            'test',
        ];

        yield[
            'prime',
        ];

        yield[
            'green',
        ];
    }

    /**
     * Provide flash message type and information if is available using test environment
     *
     * @return \Generator
     */
    public function provideFlashMessageTypeIfIsAvailableUsingTestEnvironment(): \Generator
    {
        yield[
            'test',
            false,
        ];

        yield[
            'prime',
            false,
        ];

        yield[
            'green',
            false,
        ];

        yield[
            'positive',
            true,
        ];

        yield[
            'negative',
            true,
        ];

        yield[
            'information',
            true,
        ];
    }

    /**
     * Provide flash message type if is available using defaults
     *
     * @return \Generator
     */
    public function provideFlashMessageTypeIfIsAvailableUsingDefaults(): \Generator
    {
        yield[
            'test',
            false,
        ];

        yield[
            'prime',
            false,
        ];

        yield[
            'green',
            false,
        ];

        yield[
            'primary',
            true,
        ];

        yield[
            'secondary',
            true,
        ];

        yield[
            'success',
            true,
        ];

        yield[
            'info',
            true,
        ];

        yield[
            'warning',
            true,
        ];

        yield[
            'danger',
            true,
        ];

        yield[
            'light',
            true,
        ];

        yield[
            'dark',
            true,
        ];
    }

    /**
     * Provide unavailable flash message types to verify using defaults
     *
     * @return \Generator
     */
    public function provideUnavailableFlashMessageTypesToVerify(): \Generator
    {
        yield[
            [
                'test1',
                'test2',
            ],
        ];

        yield[
            [
                'prime',
                'something',
            ],
        ];

        yield[
            [
                'green',
                'red',
                'blue',
            ],
        ];
    }

    /**
     * Provide flash message types to verify using test environment
     *
     * @return \Generator
     */
    public function provideFlashMessageTypesToVerifyUsingTestEnvironment(): \Generator
    {
        yield[
            [
                'positive',
                'negative',
                'positive',
            ],
        ];

        yield[
            [
                'information',
                'negative',
                'negative',
            ],
        ];

        yield[
            [
                'information',
            ],
        ];
    }

    /**
     * Provide flash message types to verify using defaults
     *
     * @return \Generator
     */
    public function provideFlashMessageTypesToVerifyUsingDefaults(): \Generator
    {
        yield[
            [
                'primary',
                'secondary',
                'success',
            ],
        ];

        yield[
            [
                'info',
                'success',
                'success',
            ],
        ];

        yield[
            [
                'danger',
            ],
        ];
    }

    /**
     * Provide flash messages to add using unavailable flash message type
     *
     * @return \Generator
     */
    public function provideFlashMessagesToAddUsingUnavailableFlashMessageType(): \Generator
    {
        yield[
            [
                'danger' => 'Oops, not saved',
                'test'   => 'test',
            ],
        ];

        yield[
            [
                'prime' => 'test',
            ],
        ];

        yield[
            [
                'info'     => 'test',
                'positive' => 'Saved',
            ],
        ];
    }

    /**
     * Provide flash messages to add using test environment
     *
     * @return \Generator
     */
    public function provideFlashMessagesToAddUsingTestEnvironment(): \Generator
    {
        yield[
            [
                'negative' => 'Oops, not saved',
            ],
            [
                'negative' => [
                    'Oops, not saved',
                ],
            ],
        ];

        yield[
            [
                'negative'    => 'Oops, not saved',
                'information' => 'Try again',
            ],
            [
                'negative'    => [
                    'Oops, not saved',
                ],
                'information' => [
                    'Try again',
                ],
            ],
        ];

        yield[
            [
                'positive'    => [
                    'Saved',
                    'Check your mailbox',
                ],
                'information' => 'You are registered user now',
            ],
            [
                'positive'    => [
                    'Saved',
                    'Check your mailbox',
                ],
                'information' => [
                    'You are registered user now',
                ],
            ],
        ];
    }

    /**
     * Provide flash messages to add using default configuration
     *
     * @return \Generator
     */
    public function provideFlashMessagesToAddUsingDefaults(): \Generator
    {
        yield[
            [
                'danger' => 'Oops, not saved',
            ],
            [
                'danger' => [
                    'Oops, not saved',
                ],
            ],
        ];

        yield[
            [
                'danger' => 'Oops, not saved',
                'info'   => 'Try again',
            ],
            [
                'danger' => [
                    'Oops, not saved',
                ],
                'info'   => [
                    'Try again',
                ],
            ],
        ];

        yield[
            [
                'success' => [
                    'Saved',
                    'Check your mailbox',
                ],
                'info'    => 'You are registered user now',
            ],
            [
                'success' => [
                    'Saved',
                    'Check your mailbox',
                ],
                'info'    => [
                    'You are registered user now',
                ],
            ],
        ];
    }

    /**
     * Provide positive flash messages to add using test environment
     *
     * @return \Generator
     */
    public function providePositiveFlashMessagesToAddUsingTestEnvironment(): \Generator
    {
        yield[
            [
                'Saved',
            ],
            [
                'positive' => [
                    'Saved',
                ],
            ],
        ];

        yield[
            [
                'Saved',
                'It works fine',
            ],
            [
                'positive' => [
                    'Saved',
                    'It works fine',
                ],
            ],
        ];

        yield[
            [
                'Saved',
                'It works fine',
                'Check your mailbox',
                'You are registered user now',
            ],
            [
                'positive' => [
                    'Saved',
                    'It works fine',
                    'Check your mailbox',
                    'You are registered user now',
                ],
            ],
        ];
    }

    /**
     * Provide positive flash messages to add using default configuration
     *
     * @return \Generator
     */
    public function providePositiveFlashMessagesToAddUsingDefaults(): \Generator
    {
        yield[
            [
                'Saved',
            ],
            [
                'success' => [
                    'Saved',
                ],
            ],
        ];

        yield[
            [
                'Saved',
                'It works fine',
            ],
            [
                'success' => [
                    'Saved',
                    'It works fine',
                ],
            ],
        ];

        yield[
            [
                'Saved',
                'It works fine',
                'Check your mailbox',
                'You are registered user now',
            ],
            [
                'success' => [
                    'Saved',
                    'It works fine',
                    'Check your mailbox',
                    'You are registered user now',
                ],
            ],
        ];
    }

    /**
     * Provide negative flash messages to add using test environment
     *
     * @return \Generator
     */
    public function provideNegativeFlashMessagesToAddUsingTestEnvironment(): \Generator
    {
        yield[
            [
                'Oops, not saved',
            ],
            [
                'negative' => [
                    'Oops, not saved',
                ],
            ],
        ];

        yield[
            [
                'Oops, not saved',
                'It does not work fine',
            ],
            [
                'negative' => [
                    'Oops, not saved',
                    'It does not work fine',
                ],
            ],
        ];

        yield[
            [
                'Oops, not saved',
                'It does not work fine',
                'Configuration of your mailbox is incorrect',
                'You have to register once again',
            ],
            [
                'negative' => [
                    'Oops, not saved',
                    'It does not work fine',
                    'Configuration of your mailbox is incorrect',
                    'You have to register once again',
                ],
            ],
        ];
    }

    /**
     * Provide negative flash messages to add using default configuration
     *
     * @return \Generator
     */
    public function provideNegativeFlashMessagesToAddUsingDefaults(): \Generator
    {
        yield[
            [
                'Oops, not saved',
            ],
            [
                'danger' => [
                    'Oops, not saved',
                ],
            ],
        ];

        yield[
            [
                'Oops, not saved',
                'It does not work fine',
            ],
            [
                'danger' => [
                    'Oops, not saved',
                    'It does not work fine',
                ],
            ],
        ];

        yield[
            [
                'Oops, not saved',
                'It does not work fine',
                'Configuration of your mailbox is incorrect',
                'You have to register once again',
            ],
            [
                'danger' => [
                    'Oops, not saved',
                    'It does not work fine',
                    'Configuration of your mailbox is incorrect',
                    'You have to register once again',
                ],
            ],
        ];
    }

    /**
     * Provide neutral flash messages to add using test environment
     *
     * @return \Generator
     */
    public function provideNeutralFlashMessagesToAddUsingTestEnvironment(): \Generator
    {
        yield[
            [
                'You\'ve got mail',
            ],
            [
                'information' => [
                    'You\'ve got mail',
                ],
            ],
        ];

        yield[
            [
                'You\'ve got mail',
                'Please wait',
            ],
            [
                'information' => [
                    'You\'ve got mail',
                    'Please wait',
                ],
            ],
        ];

        yield[
            [
                'You\'ve got mail',
                'Please wait',
                'This is configuration of your mailbox',
                'You have to login now',
            ],
            [
                'information' => [
                    'You\'ve got mail',
                    'Please wait',
                    'This is configuration of your mailbox',
                    'You have to login now',
                ],
            ],
        ];
    }

    /**
     * Provide neutral flash messages to add using default configuration
     *
     * @return \Generator
     */
    public function provideNeutralFlashMessagesToAddUsingDefaults(): \Generator
    {
        yield[
            [
                'You\'ve got mail',
            ],
            [
                'info' => [
                    'You\'ve got mail',
                ],
            ],
        ];

        yield[
            [
                'You\'ve got mail',
                'Please wait',
            ],
            [
                'info' => [
                    'You\'ve got mail',
                    'Please wait',
                ],
            ],
        ];

        yield[
            [
                'You\'ve got mail',
                'Please wait',
                'This is configuration of your mailbox',
                'You have to login now',
            ],
            [
                'info' => [
                    'You\'ve got mail',
                    'Please wait',
                    'This is configuration of your mailbox',
                    'You have to login now',
                ],
            ],
        ];
    }

    /**
     * Provide messages to prepare using test environment
     *
     * @return \Generator
     */
    public function provideMessagesToPrepareUsingTestEnvironment(): \Generator
    {
        yield[
            [
                'negative' => 'Oops, not saved',
            ],
            [
                'negative' => [
                    'Oops, not saved',
                ],
            ],
        ];

        yield[
            [
                'negative'    => 'Oops, not saved',
                'information' => 'Try again',
            ],
            [
                'negative'    => [
                    'Oops, not saved',
                ],
                'information' => [
                    'Try again',
                ],
            ],
        ];

        yield[
            [
                'positive'    => [
                    'Saved',
                    'Check your mailbox',
                ],
                'information' => 'You are registered user now',
            ],
            [
                'positive'    => [
                    'Saved',
                    'Check your mailbox',
                ],
                'information' => [
                    'You are registered user now',
                ],
            ],
        ];
    }

    /**
     * Provide messages to prepare using default configuration
     *
     * @return \Generator
     */
    public function provideMessagesToPrepareUsingDefaults(): \Generator
    {
        yield[
            [
                'danger' => 'Oops, not saved',
            ],
            [
                'danger' => [
                    'Oops, not saved',
                ],
            ],
        ];

        yield[
            [
                'danger' => 'Oops, not saved',
                'info'   => 'Try again',
            ],
            [
                'danger' => [
                    'Oops, not saved',
                ],
                'info'   => [
                    'Try again',
                ],
            ],
        ];

        yield[
            [
                'success' => [
                    'Saved',
                    'Check your mailbox',
                ],
                'info'    => 'You are registered user now',
            ],
            [
                'success' => [
                    'Saved',
                    'Check your mailbox',
                ],
                'info'    => [
                    'You are registered user now',
                ],
            ],
        ];
    }

    /**
     * Provide messages to prepare using test environment and unavailable flash message type
     *
     * @return \Generator
     */
    public function provideMessagesToPrepareUsingUnavailableFlashMessageType(): \Generator
    {
        yield[
            [
                'danger' => 'Oops, not saved',
                'test'   => 'test',
            ],
        ];

        yield[
            [
                'success' => 'Saved',
                'test'    => 'test',
            ],
        ];

        yield[
            [
                'positive' => [
                    'Saved',
                    'Check your mailbox',
                ],
                'test'     => 'test',
            ],
        ];
    }

    /**
     * Provide flash messages to verify if there are any flash messages
     *
     * @return \Generator
     */
    public function provideFlashMessagesToVerifyIfThereAreMessages(): \Generator
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
                'test'   => 'test',
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
    protected function setUp(): void
    {
        parent::setUp();
        static::bootKernel();
    }
}
