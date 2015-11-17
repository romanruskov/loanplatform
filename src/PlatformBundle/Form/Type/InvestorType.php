<?php

namespace PlatformBundle\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class InvestorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', 'text')
            ->add('email', 'email')
            ->add('availableForInvestments', 'number')
            ->add('avatarFile', 'file', array('required' => false))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'PlatformBundle\Entity\Investor',
            'translation_domain' => 'PlatformBundle',
            'validation_groups' => array('Default')
        ));
    }

    public function getName()
    {
        return 'platform_investor';
    }
}
