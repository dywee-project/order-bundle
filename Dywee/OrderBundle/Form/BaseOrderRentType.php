<?php

namespace Dywee\OrderBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BaseOrderRentType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->remove('orderElements')
            ->add('orderElements',      'collection',   array(
                'type'          => new OrderElementRentType(),
                'allow_add'     => true,
                'allow_delete'  => true,
                'by_reference'  => false
            ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Dywee\OrderBundle\Entity\BaseOrder'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'dywee_orderbundle_baseorderrent';
    }

    public function getParent()
    {
        return new BaseOrderType();
    }
}
