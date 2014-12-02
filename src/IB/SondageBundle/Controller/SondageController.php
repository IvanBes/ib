<?php

namespace IB\SondageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Httpfoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Templating\TemplateReference;

use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\RouteRedirectView;
use FOS\RestBundle\View\View;

use IB\SondageBundle\Form\SondageVoteType;
use IB\SondageBundle\Form\SondageVoteHandle;
use IB\SondageBundle\Entity\Sondage;

class SondageController extends Controller
{
    public function getSondageResultAction(Sondage $sondage, Request $request)
    {
        if ('json' === $request->getRequestFormat() && $request->isXmlHttpRequest()) 
        {
            $templatingHandler = function($handler, $view, $request) 
            {
                $view->setTemplate(new TemplateReference('IBSondageBundle', 'Sondage', 'sondage_stats'));
                $jsonData = array('html' => $handler->renderTemplate($view, 'html'));
                return new Response(json_encode($jsonData), Codes::HTTP_OK, $view->getHeaders());
            };

            $this->get('fos_rest.view_handler')->registerHandler('json', $templatingHandler);
        }

        return $this->onSondageView(array('sondage' => $sondage,'reponses' => $sondage->getReponses(),'totalvote' => $sondage->getTotalVote(), 'close' => $sondage->isClose()));
    }
    
    public function getSondageFormAction(Request $request)
    {	
    	$sondage = $this->getDoctrine()->getManager()->getRepository('IBSondageBundle:Sondage')->getLastSondage();

    	if ($sondage !== null) 
    	{  
            $voterManager = $this->get('ib_like.manager.voter')->init('IBSondageBundle:Sondage', $sondage->getId(), $request->getClientIp(), true);

            if ($voterManager->count() == 0 && $this->get('security.context')->isGranted("OWNER", $sondage) && !$sondage->isClose()) 
            {
                $form = $this->createForm(new SondageVoteType(), $sondage, array('choices_sondage' => $sondage->getChoices()))->createView();

                return $this->onSondageView(array('sondage' => $sondage,'form' => $form));
            }
            else
            {
                return $this->getSondageResultAction($sondage, $request);
            }
        } 
        else 
        {
            $view = View::create()
                        ->setData(array('message' => 'Il n\'y a pas de sondage.', 'success' => false))
                        ->setTemplate(new TemplateReference('IBSondageBundle', 'Sondage', 'errors'));
    
            return $this->getViewHandler()->handle($view);
        }      
    }

    public function postSondageVoteAction(Request $request)
    {
        if ($request->isXmlHttpRequest() AND 'POST' === $request->getMethod()) 
        {
            $sondage = $this->getDoctrine()->getManager()->getRepository('IBSondageBundle:Sondage')->getLastSondage();

            if (!$sondage OR $sondage->isClose())
            {
                return $this->onErrorMessage('Le sondage a était clôturé et/ou la requête est érroné.');
            }

            $voterManager = $this->get('ib_like.manager.voter')->init('IBSondageBundle:Sondage', $sondage->getId(), $request->getClientIp(), true);

            if ($voterManager->count() == 0) 
            {
                $form = $this->createForm(new SondageVoteType(), $sondage, array('choices_sondage' => $sondage->getChoices()));
                $form->setData($sondage);
                $form->bind($request);
        
                if ($form->isValid()) 
                {
                    $voterManager->putVote($sondage);
                    return $this->getSondageResultAction($sondage, $request);
                }
        
                return $this->onErrorMessage('Requête erroné.');
            }
        }

        return $this->onErrorMessage('Vous ne pouvez pas generer cette page.');           
    }

    private function onSondageView(array $data)
    {
        $view = View::create()
            ->setStatusCode(Codes::HTTP_OK)
            ->setData($data)
            ->setTemplate(new TemplateReference('IBSondageBundle', 'Sondage', 'sondage'));
        
        return $this->getViewHandler()->handle($view);
    }

    private function onErrorMessage($message)
    {
        $view = View::create()
                    ->setStatusCode(Codes::HTTP_BAD_REQUEST)
                    ->setData(array('message' => $message, 'success' => false))
                    ->setTemplate(new TemplateReference('IBSondageBundle', 'Sondage', 'errors'));

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