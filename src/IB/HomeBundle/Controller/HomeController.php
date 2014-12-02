<?php

namespace IB\HomeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HomeController extends Controller
{
	public function indexAction()
	{    
        return $this->render('::layout_accueil.html.twig');
   	}
}