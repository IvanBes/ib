<?php
namespace IB\PageBundle\Admin;
 
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\DependencyInjection\ContainerInterface; 
use Knp\Menu\ItemInterface as MenuItemInterface;
 
class ModuleAdmin extends Admin
{
	protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
                ->add('name')
                ->add('forward')
                ->add('position', 'choice',  array('choices' => array('haut' => 'Mettre le module en haut de menu',
                                                                      'bas' => 'Mettre le module en bas de menu'
                                                                    )))
                ->add('priority')
            ->end();
	}

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('name')
            ->add('forward')
            ->add('_action', 'actions', array(
                    'actions' => array(
                        'edit' => array(),
                        'delete' => array()
                    )
                ));
    }
}