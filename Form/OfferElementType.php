<?php

namespace Dywee\OrderBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OfferElementType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('quantity', NumberType::class, array('empty_data' => 1))
            ->add('product',    EntityType::class,   array(
                'class'     => 'DyweeProductBundle:Product',
                'property'  => 'name'
            ))
            /*->add('beginAt',    'date', array(
                'input' => 'datetime',
                'widget'=> 'single_text'
            ))
            ->add('endAt',    'date', array(
                'input' => 'datetime',
                'widget'=> 'single_text'
            ))*/
            ->add('discountRate', NumberType::class, array('empty_data' => 0))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Dywee\OrderBundle\Entity\OfferElement'
        ));
    }
}
