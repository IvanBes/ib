<?php

namespace IB\SchemaBundle\EventListener;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use IB\SchemaBundle\Model\JsonAjaxControllerInterface;

class JsonAjaxListener
{
    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();
        if (!is_array($controller)) { return; }

        if ($controller[0] instanceof JsonAjaxControllerInterface) 
        {
            $request = $event->getRequest();
            if('json' === $request->getRequestFormat() && !$request->isXmlHttpRequest())
            {
                throw new AccessDeniedHttpException('Vous ne pouvez pas générer cette page.');
            }
            return;
        }
    }
}