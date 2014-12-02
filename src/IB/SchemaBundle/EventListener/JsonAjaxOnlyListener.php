<?php

namespace IB\SchemaBundle\EventListener;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use IB\SchemaBundle\Model\JsonAjaxOnlyControllerInterface;

class JsonAjaxOnlyListener
{
    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();
        if (!is_array($controller)) { return; }

        if ($controller[0] instanceof JsonAjaxOnlyControllerInterface) 
        {
            $request = $event->getRequest();
            if(!('json' === $request->getRequestFormat() && $request->isXmlHttpRequest()))
            {
                throw new AccessDeniedHttpException('Vous ne pouvez pas générer cette page.');
            }
            return;
        }
    }
}