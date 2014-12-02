<?php
namespace IB\DocumentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Httpfoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use JMS\SecurityExtraBundle\Annotation\Secure;
use IB\DocumentBundle\Entity\Document;
use IB\PageBundle\Entity\Menu;

class DocumentController extends Controller
{
	public function DocumentsAction(Request $request, Menu $menu)
    {
    	$data['menu'] = $menu;
		$data['documentHasGaleries'] = null;

        $repository_document = $this->getDoctrine()->getManager()->getRepository('IBDocumentBundle:Document');
		$document = $repository_document->myFindOneByMenu($menu);

        if (!empty($document)) 
        {
            $paginator  = $this->get('knp_paginator');
            $document = current($document);

            $i = 0;
            $len = count($document['documentHasGaleries']);
    		$galerieHasMedias = [];

            $_page_section = $request->query->get('_page_section', null);
            $_page = $request->query->get('_page', null);

            $paginate = ($_page_section !== null && $_page_section !== null && 'json' === $request->getRequestFormat()) ? true:null;
            
        	foreach ($document['documentHasGaleries'] as $key => $documentHasGalerie) 
        	{
                $_paginate_this = ($paginate && $_page_section === $documentHasGalerie['galerie']['id'].'_') ? true:false;
                
                if (empty($paginate) OR $_paginate_this) 
                {
                    $galerieHasMedias[$key] = $paginator->paginate(
                            $repository_document->getPagination($documentHasGalerie['galerie'], count($documentHasGalerie['galerie']['galleryHasMedias'])),
                            ($_paginate_this) ? $_page : 1, 
                            $this->container->getParameter('ib_document.per_page')
                        );
                    $galerieHasMedias[$key]->setTemplate('KnpPaginatorBundle:Pagination:sliding_ajax.html.twig');
                    
                    if ($_paginate_this) {
                        return $this->render('IBDocumentBundle:Document:document_page.html.twig', array('pagination' =>$galerieHasMedias[$key], 'section' => $_page_section));
                    }                    
                }

          		if ($i == $len - 1) {
        			$document['documentHasGaleries'][$key]['galerie']['galleryHasMedias'] = $galerieHasMedias;
    			}

        		$i++;
        	}

        	$data['documentHasGaleries'] = $document['documentHasGaleries'];
        }

        return $this->render('IBDocumentBundle:Document:documents.html.twig', $data);
    }
}