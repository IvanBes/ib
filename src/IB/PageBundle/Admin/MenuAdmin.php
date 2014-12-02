<?php
namespace IB\PageBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;

class MenuAdmin extends Admin
{
    protected $maxPerPage = 2500;
    protected $maxPageLinks = 2500;

    public function createQuery($context = 'list')
    {
        $em = $this->modelManager->getEntityManager('IB\PageBundle\Entity\Menu');

        $queryBuilder = $em
            ->createQueryBuilder('m')
            ->select('m')
            ->from('IBPageBundle:Menu', 'm')
            ->addOrderBy('m.root', 'ASC')
            ->addOrderBy('m.lft', 'ASC');

        $query = new ProxyQuery($queryBuilder);
        return $query;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('up', 'text', array('template' => 'IBPageBundle:admin:field_tree_up.html.twig', 'label'=>'Monter'))
            ->add('down', 'text', array('template' => 'IBPageBundle:admin:field_tree_down.html.twig', 'label'=>'Descendre'))
            ->addIdentifier('laveled_title', null, array('sortable'=>false, 'label'=>'Titre de la page', 'safe' => true))
            ->add('_action', 'actions', array(
                    'actions' => array(
                        'edit' => array(),
                        'delete' => array()
                    )
                ));
    }

    protected function configureFormFields(FormMapper $form)
    {
        $subject = $this->getSubject();
        $id = $subject->getId();

        $query = function($er) use ($id) {
            $qb = $er->createQueryBuilder('m');
            if ($id){
                $qb
                    ->where('m.id <> :id')
                    ->setParameter('id', $id);
            }
            $qb
                ->orderBy('m.root, m.lft', 'ASC');
            return $qb;
        };

        $query_children = function($er) use ($subject) {
            $qb = $er->getChildrenQueryBuilder($subject);
            return $qb;
        };

        $form
            ->with('Formulaire')
                ->add('url', null, array('attr' => array('disabled'=>true)))
                ->add('parent', null, array('label' => 'Mère',
                                            'help' => '  * Votre menu doit obligatoirement avoir une mère.',
                                            'required' => true
                                          , 'query_builder' => $query
                    ));

            if($subject->getId() !== null) {
                $form->add('redirection', null, array('label' => 'Redirection',
                                        'required' => false,
                                        'query_builder' =>  $query_children
                ));
            }

        $form->add('modules', 'entity', array(
                    'class'        => 'IBPageBundle:Module',
                    'property'     => 'name',
                    'multiple'     => true,
                    'expanded'     => true
                ))
                ->add('name', null, array('label' => 'Nom'))
                ->add('contenus', 'sonata_type_collection', array(
                        'help' => '  * Vous ne pouvez pas mettre de contenu(s) si vous mettez des galerie(s).',
                        'cascade_validation' => true,
                    ), array(
                        'allow_delete' => false,
                        'multiple' => true,
                        'expanded' => false,
                        'edit'              => 'inline',
                        'inline'            => 'table',
                    )
                )
            ->add('menuHasGaleries', 'sonata_type_collection', array(
                        'help' => '* Vous ne pouvez pas mettre de galerie(s) si vous mettez du contenu(s).',
                        'cascade_validation' => true,
                    ), array(
                        'allow_delete' => false,
                        'multiple' => true,
                        'expanded' => false,
                        'edit'              => 'inline',
                        'inline'            => 'table',
                    ))
            ->end();
    }

    /**
     * {@inheritdoc}
     */
    public function prePersist($object)
    {
        $object->setContenus($object->getContenus());
        $object->setMenuHasGaleries($object->getMenuHasGaleries());
    }

    public function preRemove($object)
    {
        $em = $this->modelManager->getEntityManager($object);
        $repo = $em->getRepository("IBPageBundle:Menu");
        $subtree = $repo->childrenHierarchy($object);
        foreach ($subtree AS $el) {
            $contenus = $em->getRepository('IBPageBundle:Contenu')
                           ->findBy(array('menu'=> $el['id']));
            foreach ($services AS $s){
                $em->remove($s);
            }
            $em->flush();
        }

        $repo->verify();
        $repo->recover();
        $em->flush();
    }

    public function postPersist($object)
    {
        $object->setUrl($this->initUrl($object));
        $em = $this->modelManager->getEntityManager($object);
        $repo = $em->getRepository("IBPageBundle:Menu");
        $repo->verify();
        $repo->recover();
        $em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function preUpdate($object)
    {
        $object->setContenus($object->getContenus());
        $object->setMenuHasGaleries($object->getMenuHasGaleries());
        $this->initUrl($object);
    }

    public function postUpdate($object)
    {        
        $object->setUrl($this->initUrl($object));
        $em = $this->modelManager->getEntityManager($object);
        $repo = $em->getRepository("IBPageBundle:Menu");
        $repo->verify();
        $repo->recover();
        $em->flush();
    }

    private function initUrl($object)
    {
        $object_pattern = $object;
        $url_pattern = '';

        for ($i=0; $i < $object->getLvl(); $i++) 
        {   
            $url_pattern = ($i+1 == $object->getLvl()) ? $object_pattern->getSlug().''.$url_pattern : '/'.$object_pattern->getSlug().''.$url_pattern;
            $object_pattern = $object_pattern->getParent();
        }

        return $url_pattern;
    }
}