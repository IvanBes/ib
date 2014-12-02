<?php

namespace IB\SchemaBundle\EventListener;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use IB\SchemaBundle\Model\DoPreActionListenerInterface;

class DoPreActionListener
{
    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();
        if (!is_array($controller)) { return; }

        if ($controller[0] instanceof DoPreActionListenerInterface) 
        {
            $do = $controller[0]::$pre_action;
            $controller[0]->$do();
            return;
        }
    }
}