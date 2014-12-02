<?php

namespace IB\LikeBundle\Controller;

use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\RouteRedirectView;
use FOS\RestBundle\View\View;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class LikeController extends Controller
{
    public function putLikeAction($driver, Request $request)
    {
        if ($request->isXmlHttpRequest() && ('json' === $request->getRequestFormat()) && ('PUT' === $request->getMethod()))
        {  
            $token = $request->request->get('_token');
        
            if (!$this->get('form.csrf_provider')->isCsrfTokenValid('put_iblikebundle_'.$driver.'_OFsa780Wtnl', $token)) 
            {
                return $this->onErrorMessage('Jeton invalide.');
            }

            $ip = $request->getClientIp();
            $id = $request->request->get('ib_like_id', null);
            
            $voterManager = $this->get('ib_like.manager.voter')->init($driver, $id, $ip);

            if (true === $this->get('security.context')->isGranted("OWNER", $voterManager->getClassName()))
            { 
                $count = ($id !== 0) ? $voterManager->count() : 1;

                if ($count == 0) 
                {
                    $object = $voterManager->findEntityByNameAndId();
        
                    $featureRate = $object->getRating()+1;
                    $object->setRatingByIncrement();
                    $voterManager->putVote($object);
                    
                    $view = View::create()->setData(array('success' => true, 'rate' => $featureRate));
                    return $this->getViewHandler()->handle($view);
                }
                return $this->onErrorMessage('Vous avez déjà voté.');
            }

            return $this->onErrorMessage('Requête erroné.'); 
        }

        return $this->onErrorMessage('Vous ne pouvez pas generer cette page.');
    }

    protected function onErrorMessage($message)
    {
        $view = View::create()
                    ->setStatusCode(Codes::HTTP_BAD_REQUEST)
                    ->setData(array('message' => $message, 'success' => false));

                return $this->getViewHandler()->handle($view);
    }

    /**
     * @return \FOS\RestBundle\View\ViewHandler
     */
    private function getViewHandler()
    {
        return $this->container->get('fos_rest.view_handler');
    }
}