<?php
namespace IB\PageBundle\Admin;
 
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Route\RouteCollection;
 
class ContenuAdmin extends Admin
{
    /**
     * @var object
     */
    protected $container;

    public $supportsPreviewMode = true;

	public function __construct($code, $class, $baseControllerName)
    {
        parent::__construct($code, $class, $baseControllerName);
    }
    
	protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
                ->add('titre', null, array('attr' => array('class' => 'span12')))
                ->add('contenu', 'sonata_formatter_type', array(
                    'source_field'         => 'rawContenu',
                    'source_field_options' => array('attr' => array('class' => 'span8', 'rows' => 15)),
                    'format_field'         => 'contenuFormatter',
                    'target_field'         => 'contenu',
                    'ckeditor_context'     => 'default',
                    'event_dispatcher'     => $formMapper->getFormBuilder()->getEventDispatcher(),
                    'help' => 'Vous devez uniquement créer des contenus dans la section "Menu".'
                ))
            ->end();
    }
    
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->clearExcept(array('create', 'edit'));
    }

    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('titre')
            ->add('contenuFormatter')
            ->add('contenu', null, array('safe' => true))
            ->add('publier');
    }
}