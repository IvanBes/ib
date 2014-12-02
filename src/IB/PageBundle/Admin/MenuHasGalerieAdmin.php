<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace IB\PageBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;

class MenuHasGalerieAdmin extends Admin
{
    /**
     * @param \Sonata\AdminBundle\Form\FormMapper $formMapper
     *
     * @return void
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $link_parameters = array();

        if ($this->hasParentFieldDescription()) {
            $link_parameters = $this->getParentFieldDescription()->getOption('link_parameters', array());
        }

        if ($this->hasRequest()) {            
            $context = $this->getRequest()->get('context', null);

            if (null !== $context) {                
                $link_parameters['context'] = $context;
            }
        }

        $formMapper
            ->add('galerie', 'sonata_type_model_list', array('required' => false,), array(
                'link_parameters' => $link_parameters,
            ));
    }

    /**
     * @param  \Sonata\AdminBundle\Datagrid\ListMapper $listMapper
     * @return void
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('menu')
            ->add('galerie')
        ;
    }

    public function prePersist($menuHasGalerie)
    {
       if ($menuHasGalerie->getGalerie()->getContext() !== 'galerie') {
           throw new \Exception("Context de galerie uniquement.", 1);
       }

       return;
    }
}