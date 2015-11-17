<?php

namespace PlatformBundle\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class InvestmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('amount', 'number', array('data' => 2.95, 'scale' => 2))
            ->add('plainLoanId', 'number')
            ->add('loanSearchQuery', 'text', array('required' => false))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'PlatformBundle\Entity\Investment',
            'translation_domain' => 'PlatformBundle'
        ));
    }

    public function getName()
    {
        return 'platform_investment';
    }
}
