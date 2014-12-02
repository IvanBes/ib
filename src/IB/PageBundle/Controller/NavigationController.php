<?php
namespace IB\PageBundle\Controller;

use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\RouteRedirectView;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations\Route;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Templating\TemplateReference;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Response;

use IB\SchemaBundle\Model\JsonAjaxControllerInterface;

 class NavigationController extends Controller implements JsonAjaxControllerInterface
 {
    private $_menu_transversale = false;

    /**
     * Obtenir le menu latérale
     * @param Request $request Current request
     */
    public function getMenuLateraleAction(Request $request)
    {   
        $data['menu_laterale'] = $this->getDoctrine()->getManager()->getRepository('IBPageBundle:Menu')->getMenuLaterale();

        $view = View::create()
            ->setData($data)
            ->setTemplate(new TemplateReference('IBPageBundle', 'Menu', 'menu_laterale'));

        return $this->getViewHandler()->handle($view);
    }

    /**
     * Obtenir le menu footer
     * @param Request $request Current request
     */
    public function getMenuFooterAction(Request $request)
    {   
        $data['menu_footer'] = $this->getDoctrine()->getManager()->getRepository('IBPageBundle:Menu')->getMenuFooter();

        $view = View::create()
            ->setData($data)
            ->setTemplate(new TemplateReference('IBPageBundle', 'Menu', 'menu_footer'));

        return $this->getViewHandler()->handle($view);
    }

    /**
     * Obtenir le menu latérale
     * @param Request $request Current request
     */
    public function getMenuTransversaleAction(Request $request, $slug)
    {
        $data['menu_transversale'] = null;
        $data['slug'] = $slug;

        if ($slug == null) {
            throw new \Exception("Error Processing Request", 1);
        } 

        $menu_repository = $this->getDoctrine()->getManager()->getRepository('IBPageBundle:Menu');
        $data['menu_laterale_select'] = $menu_repository->getCheckMenuTransversaleBySlug($slug);

        if (!empty($data['menu_laterale_select']))
        {   
            $route = $this->get('router');
            $childrenIndex = $menu_repository->getChildrenIndex();

            $build_modules = function($node, $position) use ($route)
            {
                $html_modules = '';

                foreach ($node['modules'] as $module) 
                {
                    if($module['position'] == $position) 
                    {
                        $route = 'href="'.$route->generate('ib_page_get_repertoire', array('url' => $node['url'], 'module' => $module['name'])).'" class="ajaxLink"';
                        $html_modules .= '<li><a '.$route.'><span class="menu-item-parent">'.$module['name'].'</span></a></li>';
                    }
                }
                return $html_modules;
            };

            $data['menu_transversale'] = $menu_repository->menuChildrenHierarchy($data['menu_laterale_select'], false, 
                array(
                    'decorate' => true,
                    'rootOpen' => function ($tree, $parent) use (&$build_modules) {
                        $split = $tree[0]['lvl'] + 2;
                        return '<ul class="nav-selected-partiel" data-split="'. $split .'" >'.$build_modules($parent, 'haut');
                    },
                    'nodeDecorator' => function ($node) use ($route, $childrenIndex) {
                        $url_transversale = ($node['redirection'] !== null) ? $node['redirection']['url'] : $node['url'];
                        $route = (count($node[$childrenIndex]) > 0) ? null : 'href="'.$route->generate('ib_page_get_repertoire', array('url' => $url_transversale)).'" class="ajaxLink"';
                        return '<a '.$route.' title="'.$node['name'].'"><span class="menu-item-parent">'.$node['name'].'</span></a>';
                    },
                    'rootClose' => function ($tree, $parent) use (&$build_modules) {
                        return $build_modules($parent, 'bas').'</ul>';
                    }
                ), true);
            
            $this->_menu_transversale = true;
        }

        if ('json' === $request->getRequestFormat()) 
        {         
           return new response($data['menu_transversale']);
        }

        $view = View::create()
            ->setData($data)
            ->setTemplate(new TemplateReference('IBPageBundle', 'Include', 'menu_transversale'));

        return $this->getViewHandler()->handle($view);
    }

    /**
     * Obtenir le menu latérale
     * @param Request $request Current request
     * @Route(requirements={"url"="[^.]+"})
     */
    public function getRepertoireAction(Request $request, $url)
    {
        $data['menu'] = $this->getDoctrine()->getManager()->getRepository('IBPageBundle:Menu')->getRepertoire($url);

        if (null == $data['menu']) {throw new NotFoundHttpException();}
        
        $_array_url = explode('/', $url);
        $data['menu_name'] = $_array_url[0];

        $_moduleURI = urldecode($request->query->get('module', null));

        if (!empty($_moduleURI))
        {
            $_module = $this->getModuleByName($data['menu']->getModules(), $_moduleURI);
            if(!empty($_module))
            {
                $data['module_html'] = $this->forward($_module->getForward(), array('request' => $request, 'menu' => $data['menu']))->getContent();                
            }
        }

        if ('json' === $request->getRequestFormat()) 
        {
            $data['menu_transversale'] = "no_change";
            $_menu_transversale_page_before = urldecode($request->query->get('_menu_transversale_page', null));

            if (count($_array_url) == 0 OR ($_menu_transversale_page_before !== $data['menu_name'])) 
            {   
                $data['menu_transversale'] = $this->getMenuTransversaleAction($request, $data['menu_name'])->getContent();
            }

            $templatingHandler = function($handler, $view, $request) 
            {                    
                $view->setTemplate(new TemplateReference('IBPageBundle', 'Include', 'contenus'));
                $data = $view->getData();
                $view->setData(array(
                    'html' => $handler->renderTemplate($view, 'html'),
                    'menu_transversale' => $data['menu_transversale'],
                    'menu_name' => $data['menu_name'],
                    'menu_transversale_open' => $data['menu_transversale_open']
                    ));
                return $handler->createResponse($view, $request, 'json');
            };

            $this->get('fos_rest.view_handler')->registerHandler('json', $templatingHandler);
        } else if ($data['menu']->getRedirection() !== null OR ($_moduleURI == null && count($data['menu']->getContenus()) == 0 && count($data['menu']->getMenuHasGaleries()) == 0 && count($data['menu']->getModules()) > 0)) {
            
            if(count($data['menu']->getModules()) > 0 && $data['menu']->getRedirection() == null)
            {                
                $module = $data['menu']->getModules();
                $params = array('module' => $module{0}->getName(), 'url' => $data['menu']->getUrl());
            } else {
                $params = array('url' => $data['menu']->getRedirection()->getUrl());
            }

            return $this->getViewHandler()->handle($this->onRedirection($request, $params));   
        
        } else {
            $data['menu_transversale'] = $this->getMenuTransversaleAction($request, $data['menu_name'])->getContent();            
        }

        $data['menu_transversale_open'] = $this->_menu_transversale;

        $view = View::create()
            ->setData($data)
            ->setTemplate(new TemplateReference('IBPageBundle', 'Menu', 'contenus'));

        return $this->getViewHandler()->handle($view);
    }

    private function getModuleByName($modules, $search)
    {
        foreach ($modules as $module) {
            
            if ($module->getName() == $search) {
                return $module;
            }
        }
    }

    /**
     *
     * @return View
     */
    private function onRedirection(Request $request, $params)
    {        
        $params = array_merge(array('request' => $request), $params);
        return RouteRedirectView::create('ib_page_get_repertoire', $params, Codes::HTTP_FOUND);
    }

    /**
     * @return \FOS\RestBundle\View\ViewHandler
     */
    private function getViewHandler()
    {
        return $this->container->get('fos_rest.view_handler');
    }
}