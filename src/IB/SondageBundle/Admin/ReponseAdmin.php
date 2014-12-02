<?php
namespace IB\SondageBundle\Admin;
 
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\DependencyInjection\ContainerInterface; 
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Route\RouteCollection;
 
class ReponseAdmin extends Admin
{
	protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
                ->add('reponse', null, array('help' => 'Vous devez uniquement créer des réponses dans la section "sondage".'))
            ->end();
	}
	
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->clearExcept(array('create', 'edit'));
    }
}