<?php

namespace PlatformBundle\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', 'text')
            ->add('email', 'email')
            ->add('plainPassword', 'repeated', array('type' => 'password'))
            ->add('availableForInvestments', 'number', array('data' => $this->randomFloat(100, 1000), 'scale' => 2))
        ;
    }

    private function randomFloat ($min, $max)
    {
        return ($min+lcg_value()*(abs($max-$min)));
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
        return 'platform_auth_register';
    }
}
