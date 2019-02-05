<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Meritoo\Test\FlashBundle\Exception;

use Meritoo\Common\Test\Base\BaseTestCase;
use Meritoo\Common\Type\OopVisibilityType;
use Meritoo\FlashBundle\Exception\UnavailableFlashMessageTypeException;

/**
 * Test case of an exception used while type of flash message is unavailable
 *
 * @author    Meritoo <github@meritoo.pl>
 * @copyright Meritoo
 *
 * @internal
 * @coversNothing
 */
class UnavailableFlashMessageTypeExceptionTest extends BaseTestCase
{
    /**
     * @covers \Meritoo\FlashBundle\Exception\UnavailableFlashMessageTypeException::__construct
     */
    public function testConstructorVisibilityAndArguments(): void
    {
        static::assertConstructorVisibilityAndArguments(
            UnavailableFlashMessageTypeException::class,
            OopVisibilityType::IS_PUBLIC,
            3,
            0
        );
    }

    /**
     * @param string $flashMessageType           Unavailable type of flash message
     * @param array  $availableFlashMessageTypes Available flash message types
     * @param string $expectedMessage            Expected message of exception
     *
     * @dataProvider provideUnavailableFlashMessageTypeAndExpectedMessage
     * @covers       \Meritoo\FlashBundle\Exception\UnavailableFlashMessageTypeException::create
     */
    public function testCreate(
        string $flashMessageType,
        array $availableFlashMessageTypes,
        string $expectedMessage
    ): void {
        $exception = UnavailableFlashMessageTypeException::create($flashMessageType, $availableFlashMessageTypes);
        static::assertSame($expectedMessage, $exception->getMessage());
    }

    /**
     * Provides unavailable flash message type and expected message of exception
     *
     * @return \Generator
     */
    public function provideUnavailableFlashMessageTypeAndExpectedMessage(): \Generator
    {
        $template = 'The \'%s\' type of flash message is unavailable. Available types: %s. Can you use one of them?';

        yield[
            '',
            [],
            sprintf($template, '', ''),
        ];

        yield[
            'test',
            [
                'test-1',
                'test-2',
                'test-3',
            ],
            sprintf($template, 'test', 'test-1, test-2, test-3'),
        ];

        yield[
            'another test',
            [
                'another-test-1',
                'another-test-2',
            ],
            sprintf($template, 'another test', 'another-test-1, another-test-2'),
        ];
    }
}
