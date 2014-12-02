<?php
namespace IB\DocumentBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;

class DocumentAdmin extends Admin
{
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id');
    }

    protected function configureFormFields(FormMapper $form)
    {
        $q = $this->modelManager->getEntityManager("IBPageBundle:Menu")
                ->getRepository("IBPageBundle:Menu")
                ->createQueryBuilder('m')
                ->innerJoin('m.modules', 'mo')
                ->addselect('mo')
                ->where('mo.name = :module')
                ->setParameter('module', 'document');

        $form
            ->add('menu', null, array(
                                        'required' => true,
                                        'query_builder' => $q
                    ))
            ->add('documentHasGaleries', 'sonata_type_collection', array(
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
        $object->setDocumentHasGaleries($object->getDocumentHasGaleries());
    }

    /**
     * {@inheritdoc}
     */
    public function preUpdate($object)
    {
        $object->setDocumentHasGaleries($object->getDocumentHasGaleries());
    }
}