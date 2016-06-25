<?php

namespace Dywee\OrderBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
            ->add('orderElements',      CollectionType::class,   array(
                'type'          => OrderElementType::class,
                'allow_add'     => true,
                'allow_delete'  => true,
                'by_reference'  => false
            ))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Dywee\OrderBundle\Entity\BaseOrder'
        ));
    }

    public function getParent()
    {
        return BaseOrderType::class;
    }
}
