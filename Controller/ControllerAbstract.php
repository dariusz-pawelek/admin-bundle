<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FSi\Bundle\AdminBundle\Controller;

use FSi\Bundle\AdminBundle\Admin\Context\ContextInterface;
use FSi\Bundle\AdminBundle\Admin\Context\ContextManager;
use FSi\Bundle\AdminBundle\Admin\Element;
use FSi\Bundle\AdminBundle\Event\AdminEvent;
use FSi\Bundle\AdminBundle\Event\AdminEvents;
use FSi\Bundle\AdminBundle\Exception\ContextException;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

abstract class ControllerAbstract
{
    /**
     * @var EngineInterface
     */
    protected $templating;

    /**
     * @var ContextManager
     */
    protected $contextManager;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @param \Symfony\Bundle\FrameworkBundle\Templating\EngineInterface $templating
     * @param \FSi\Bundle\AdminBundle\Admin\Context\ContextManager $contextManager
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        EngineInterface $templating,
        ContextManager $contextManager,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->templating = $templating;
        $this->contextManager = $contextManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param \FSi\Bundle\AdminBundle\Admin\Element $element
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param string $route
     * @return null|\Symfony\Component\HttpFoundation\Response
     */
    protected function handleRequest(Element $element, Request $request, $route)
    {
        $event = new AdminEvent($element, $request);
        $this->eventDispatcher->dispatch(AdminEvents::CONTEXT_PRE_CREATE, $event);
        if ($event->hasResponse()) {
            return $event->getResponse();
        }
        $context = $this->contextManager->createContext($route, $element);
        if (!($context instanceof ContextInterface)) {
            throw new NotFoundHttpException(sprintf(
                'Cannot find context builder that supports element with id "%s"',
                $element->getId()
            ));
        }

        $response = $context->handleRequest($request);
        if ($response instanceof Response) {
            return $response;
        }

        if (!$context->hasTemplateName()) {
            throw new ContextException(sprintf(
                "Context %s neither returned a response nor has a template name",
                get_class($context),
                __CLASS__
            ));
        }

        return $this->templating->renderResponse(
            $context->getTemplateName(),
            $context->getData()
        );
    }
}
