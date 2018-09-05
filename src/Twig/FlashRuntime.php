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
     * Class constructor
     *
     * @param FlashMessageService $flashMessageService      Service related to flash messages
     * @param RequestStack        $requestStack             Request stack that controls the lifecycle of requests
     * @param SessionInterface    $session                  The session
     * @param EngineInterface     $templatingEngine         Engine that render templates
     * @param string              $manyMessagesTemplatePath Path of template for many flash messages (with container)
     */
    public function __construct(
        FlashMessageService $flashMessageService,
        RequestStack $requestStack,
        SessionInterface $session,
        EngineInterface $templatingEngine,
        string $manyMessagesTemplatePath
    ) {
        $this->flashMessageService = $flashMessageService;
        $this->requestStack = $requestStack;
        $this->session = $session;
        $this->templatingEngine = $templatingEngine;
        $this->manyMessagesTemplatePath = $manyMessagesTemplatePath;
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
            ->prepareMessages($messages);

        $parameters = [
            'messages' => $messages,
        ];

        return $this
            ->templatingEngine
            ->render($this->manyMessagesTemplatePath, $parameters);
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
                /* @var Session $session */
                $session = $this->session;

                $messages = $session
                    ->getFlashBag()
                    ->all();

                $this
                    ->flashMessageService
                    ->prepareMessages($messages);
            }
        }

        return $this->renderFlashMessages($messages);
    }
}
