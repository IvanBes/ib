<?php
namespace IB\SondageBundle\Admin;
 
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\DependencyInjection\ContainerInterface; 
use Knp\Menu\ItemInterface as MenuItemInterface;
use IB\SondageBundle\Entity\Sondage;
 
class SondageAdmin extends Admin
{
	protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
                ->add('question')
                ->add('reponses', 'sonata_type_collection', array(
                    'cascade_validation' => true,
                ), array(
                    'edit'              => 'inline',
                    'inline'            => 'table',
                ))
            ->end();
	}

    /**
     * {@inheritdoc}
     */
    public function prePersist($sondage)
    {
        $sondage->setReponses($sondage->getReponses());
    }
	
	protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('date', null, array('label' => 'Créee en'))
            ->add('question')
            ->add('close', null, array('editable' => true, 'label' => 'Clôturé ?'))
            ->add('_action', 'actions', array(
                'actions' => array(
                    'edit' => array(),
                    'delete' => array(),
                )
            ));
    }
}