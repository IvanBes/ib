<?php

namespace IB\PageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class PageTreeSortController extends Controller
{
    /**
    * @Security("has_role('ROLE_ADMIN')")
    */
    public function upAction($page_id)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('IBPageBundle:Menu');
        $page = $repo->findOneById($page_id);
        if ($page->getParent()){
            $repo->moveUp($page);
        }
        return $this->redirect($this->getRequest()->headers->get('referer'));
    }

    /**
    * @Security("has_role('ROLE_ADMIN')")
    */
    public function downAction($page_id)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('IBPageBundle:Menu');
        $page = $repo->findOneById($page_id);
        if ($page->getParent()){
            $repo->moveDown($page);
        }
        return $this->redirect($this->getRequest()->headers->get('referer'));
    }

    public function planAction()
    {                
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('IBPageBundle:Menu');

        $route = $this->get('router');
        $childrenIndex = $repo->getChildrenIndex();

        $data['plan'] = $repo->childrenHierarchy(null, false, 
            array(
                'decorate'=>true,
                'nodeDecorator'=> function ($node) use ($route, $childrenIndex) {
                    $route = (count($node[$childrenIndex]) > 0) ? null : 'href="'.$route->generate('ib_page_get_repertoire', array('url' => $node['url'])).'"';
                    return '<a '.$route.'>'.$node['name'].'</a>';
                },
            ));
        return $this->render('IBPageBundle:Include:plan.html.twig', $data);
    }
}