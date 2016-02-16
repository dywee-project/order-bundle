<?php

namespace Dywee\OrderBundle\Form;

use Dywee\ProductBundle\Repository\ProductRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderElementType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */

    private $website;

    public function __construct($website)
    {
        $this->website = $website;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $website = $this->website;
        $builder
            ->add('quantity', 'number', array('empty_data' => 1))
            ->add('product',    'genemu_jqueryselect2_entity',   array(
                'class'     => 'DyweeProductBundle:Product',
                'property'  => 'completeName',
                'query_builder' => function(ProductRepository $r) use ($website) {
                    return $r->getSelectList($website);
                },
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
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
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
