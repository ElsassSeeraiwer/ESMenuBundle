<?php

namespace ElsassSeeraiwer\ESMenuBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class NewMenuElementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array(
                'attr'                  => array('class' => 'span12'),
                'label'                 => 'form.new.title',
                'translation_domain'    => 'ESMenu',
                'required'              => true
            ))
            ->add('save', 'submit')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ElsassSeeraiwer\ESMenuBundle\Entity\MenuElement'
        ));
    }

    public function getName()
    {
        return 'elsassseeraiwer_esmenubundle_newmenuelementtype';
    }
}
