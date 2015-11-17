<?php

namespace PlatformBundle\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class LoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', 'text')
            ->add('plainPassword', 'password')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'PlatformBundle\Entity\Investor',
            'translation_domain' => 'PlatformBundle'
        ));
    }

    public function getName()
    {
        return 'platform_auth_login';
    }
}
