<?php

namespace IB\CommentaireBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CommentaireType extends AbstractType
{
    private $commentClass;

    public function __construct($commentClass)
    {
        $this->commentClass = $commentClass;
    }

    /**
     * Configures a Comment form.
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('commentaire', 'textarea', array('attr' => 
            array('placeholder' => 'Laisser un commentaire...', 'class' => 'form-control'),
            'label' => false
            ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);
        $resolver->setDefaults(array(
            'data_class' => $this->commentClass,
            'intention' => 'ib_commentaire_commentaire.formType'
        ));
    }

    public function getName()
    {
        return "ib_commentaire_commentaire";
    }
}