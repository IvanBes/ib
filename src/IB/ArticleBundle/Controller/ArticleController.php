<?php
namespace IB\ArticleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Httpfoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use JMS\SecurityExtraBundle\Annotation\Secure;
use IB\ArticleBundle\Entity\Article;
use IB\PageBundle\Entity\Menu;

class ArticleController extends Controller
{
	public function ArticlesAction(Request $request, Menu $menu)
    {   
        $repository_article = $this->getDoctrine()->getManager()->getRepository('IBArticleBundle:Article');
        
            $articlesQuery = $repository_article->getArticles($menu);
            $paginator  = $this->get('knp_paginator');
            $data['articles'] = $paginator->paginate($articlesQuery, intval($request->query->get('page', 1)), $this->container->getParameter('ib_article.per_page'));
            $data['menu'] = $menu;

        return $this->render('IBArticleBundle:Article:articles.html.twig', $data);
    }
               
    public function ArticleAction($slug)       
 	{    
        $em = $this->getDoctrine()->getManager();
            
        $data['article'] = $em->getRepository('IBArticleBundle:Article')->getArticleBySlug($slug);

        $classname = get_class($data['article']);
        if (preg_match('@\\\\([\w]+)$@', $classname, $matches)) 
        {
            $data['classname'] = $matches[1];
            return $this->render('IBArticleBundle:Article:article.html.twig', $data);
        }

        throw new \LogicException('Erreur interne.');
    }
}