<?php
namespace IB\ArticleBundle\Admin;
 
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Knp\Menu\ItemInterface as MenuItemInterface;
use IB\ArticleBundle\Entity\Article;
 
class ArticleAdmin extends Admin
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
    
    public function prePersist($article)
    {
        $user_courant = $this->getConfigurationPool()->getContainer()->get('security.context')->getToken()->getUser();
        $article->setAccount($user_courant);
    }
	
	protected function configureFormFields(FormMapper $formMapper)
    {

        $q = $this->modelManager->getEntityManager("IBPageBundle:Menu")
                ->getRepository("IBPageBundle:Menu")
                ->createQueryBuilder('m')
                ->innerJoin('m.modules', 'mo')
                ->addselect('mo')
                ->where('mo.name = :module')
                ->setParameter('module', 'article');

        $formMapper
                ->add('titre')
                ->add('contenu', 'sonata_formatter_type', array(
                    'source_field'         => 'rawContenu',
                    'source_field_options' => array('attr' => array('class' => 'span8', 'rows' => 15)),
                    'format_field'         => 'contenuFormatter',
                    'target_field'         => 'contenu',
                    'ckeditor_context'     => 'default',
                    'event_dispatcher'     => $formMapper->getFormBuilder()->getEventDispatcher()
                ))
                ->add('menus', 'entity', array('label' => 'Menus',
                                        'required' => true,
                                        'class' => 'IBPageBundle:Menu',
                                        'property'     => 'name',
                                        'multiple'     => true,
                                        'expanded'     => true,
                                        'query_builder' =>  $q
                ))
                ->add('publier', null, array('required' => false))
            ->end();
    }
    
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->addIdentifier('titre')
            ->add('account.username')
            ->add('date', null, array('label' => 'Créee en'))
            ->add('modificationDate', null, array('label' => 'Modifié le')) 
            ->add('publier', null, array('editable' => true))
            ->add('_action', 'actions', array(
                'actions' => array(
                    'edit' => array(),
                    'delete' => array(),
                )
            ));
    }

    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('account.username', null, array('label' => 'Auteur'))
            ->add('titre')
            ->add('contenuFormatter')
            ->add('contenu', null, array('safe' => true))
            ->add('publier');
    }
}