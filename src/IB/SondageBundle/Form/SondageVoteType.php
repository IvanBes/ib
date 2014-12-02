<?php

namespace IB\SondageBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SondageVoteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
	{
$transformer = new StringToArrayTransformer();
$builder->add($builder->create('voteChoice', 'choice', array(
                'expanded' => true,
                'choices' => $options['choices_sondage']
              ))->addModelTransformer($transformer));
	}

	public function getName()
	{
		return 'ib_SondageBundle_sondagevotetype';
	}

	 public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
				'data_class' => 'IB\SondageBundle\Entity\Sondage',
				'intention' => 'IBSondageBundle_SondageVote',
				'choices_sondage' => array(),
		));
	}
}