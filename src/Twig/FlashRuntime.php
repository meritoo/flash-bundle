<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Meritoo\FlashBundle\Twig;

use Meritoo\FlashBundle\Service\FlashMessageService;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Templating\EngineInterface;
use Twig\Extension\RuntimeExtensionInterface;

/**
 * Runtime class related to FlashExtension Twig Extension.
 * Required to create lazy-loaded Twig Extension.
 *
 * @author    Meritoo <github@meritoo.pl>
 * @copyright Meritoo
 */
class FlashRuntime implements RuntimeExtensionInterface
{
    /**
     * Service related to flash messages
     *
     * @var FlashMessageService
     */
    private $flashMessageService;

    /**
     * Request stack that controls the lifecycle of requests
     *
     * @var RequestStack
     */
    private $requestStack;

    /**
     * The session
     *
     * @var SessionInterface
     */
    private $session;

    /**
     * Engine that render templates
     *
     * @var EngineInterface
     */
    private $templatingEngine;

    /**
     * Path of template for many flash messages (with container)
     *
     * @var string
     */
    private $manyMessagesTemplatePath;

    /**
     * Path of template for single/one flash message only
     *
     * @var string
     */
    private $singleMessagesTemplatePath;

    /**
     * Class constructor
     *
     * @param FlashMessageService $flashMessageService        Service related to flash messages
     * @param RequestStack        $requestStack               Request stack that controls the lifecycle of requests
     * @param SessionInterface    $session                    The session
     * @param EngineInterface     $templatingEngine           Engine that render templates
     * @param string              $manyMessagesTemplatePath   Path of template for many flash messages (with container)
     * @param string              $singleMessagesTemplatePath Path of template for single/one flash message only
     */
    public function __construct(
        FlashMessageService $flashMessageService,
        RequestStack $requestStack,
        SessionInterface $session,
        EngineInterface $templatingEngine,
        string $manyMessagesTemplatePath,
        string $singleMessagesTemplatePath
    ) {
        $this->flashMessageService = $flashMessageService;
        $this->requestStack = $requestStack;
        $this->session = $session;
        $this->templatingEngine = $templatingEngine;
        $this->manyMessagesTemplatePath = $manyMessagesTemplatePath;
        $this->singleMessagesTemplatePath = $singleMessagesTemplatePath;
    }

    /**
     * Returns rendered given flash messages
     *
     * @param array $messages Flash messages to render. Key-value pairs:
     *                        - key - type of flash message
     *                        - value - flash message
     * @return string
     */
    public function renderFlashMessages(array $messages): string
    {
        /*
         * No messages provided?
         * Nothing to do
         */
        if (empty($messages)) {
            return '';
        }

        $this
            ->flashMessageService
            ->prepareMessages($messages)
        ;

        $parameters = [
            'messages'                => $messages,
            'single_message_template' => $this->singleMessagesTemplatePath,
        ];

        return $this
            ->templatingEngine
            ->render($this->manyMessagesTemplatePath, $parameters)
        ;
    }

    /**
     * Returns rendered flash messages stored in session
     *
     * @return string
     */
    public function renderFlashMessagesFromSession(): string
    {
        $messages = [];
        $request = $this->requestStack->getCurrentRequest();

        if (null !== $request) {
            $hasSession = $request->hasPreviousSession();

            if ($hasSession) {
                /** @var Session $session */
                $session = $this->session;

                $messages = $session
                    ->getFlashBag()
                    ->all()
                ;

                $this
                    ->flashMessageService
                    ->prepareMessages($messages)
                ;
            }
        }

        return $this->renderFlashMessages($messages);
    }

    /**
     * Returns information if there are any flash messages to display (in bag/container stored in session)
     *
     * @return bool
     */
    public function hasFlashMessages(): bool
    {
        return $this->flashMessageService->hasFlashMessages();
    }
}
