<?php

namespace Dywee\OrderBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class OfferRentType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->remove('offerElements')
            ->add('offerElements',      'collection',   array(
                'type'          => new OfferElementRentType(),
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
            'data_class' => 'Dywee\OrderBundle\Entity\Offer'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'dywee_orderbundle_offerrent';
    }

    public function getParent()
    {
        return new OfferType();
    }
}
