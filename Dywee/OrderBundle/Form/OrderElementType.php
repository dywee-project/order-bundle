<?php

namespace Dywee\OrderBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderElementType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('quantity', 'number', array('empty_data' => 1))
            ->add('product',    'genemu_jqueryselect2_entity',   array(
                'class'     => 'DyweeProductBundle:Product',
                'property'  => 'name'
            ));
            /*->add('beginAt',    'date', array(
                'input' => 'datetime',
                'widget'=> 'single_text'
            ))
            ->add('endAt',    'date', array(
                'input' => 'datetime',
                'widget'=> 'single_text'
            ))*/
            $builder->add('discountRate', 'number', array('empty_data' => 0))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function configureOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Dywee\OrderBundle\Entity\OrderElement'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'dywee_orderbundle_orderelement';
    }
}
