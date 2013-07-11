<?php

namespace ElsassSeeraiwer\ESMenuBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class ModifyMenuConfigType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            /*->add('configId', 'text', array(
                'attr'                  => array('class' => 'span12'),
                'label'                 => 'form.new.configId',
                'translation_domain'    => 'ESMenu',
                'required'              => true
            ))*/
            ->add('options', 'textarea', array(
                'attr'                  => array('class' => 'span12'),
                'label'                 => 'form.new.configOptions',
                'translation_domain'    => 'ESMenu',
                'required'              => false
            ))
            ->add('menu', 'entity', array(
                'attr'                  => array('class' => 'span12'),
                'label'                 => 'form.new.menu',
                'translation_domain'    => 'ESMenu',
                'required'              => true,
                'class'                 => 'ElsassSeeraiwerESMenuBundle:MenuElement',
                'group_by'              => 'lvl',
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('m')
                        ->orderBy('m.lvl', 'ASC')
                        ->addOrderBy('m.id', 'ASC');
                },
            ))
            ->add('save', 'submit')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ElsassSeeraiwer\ESMenuBundle\Entity\MenuConfig'
        ));
    }

    public function getName()
    {
        return 'elsassseeraiwer_esmenubundle_modifymenuconfigtype';
    }
}
