<?php

namespace Dywee\OrderBundle\Form;

use Dywee\AddressBundle\Form\AddressType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class OfferType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('discountRate')
            ->add('discountValue')
            ->add('description',        'textarea',     array('required' => false))
            ->add('state',              'choice',       array('choices' => array(0 => 'Annulée', 1 => 'Proposée', 2 => 'Validée')))
            ->add('address',            'entity',       array('class' => 'DyweeAddressBundle:Address', 'property' => 'formValue'))
            ->add('offerElements',      'collection',   array(
                'type'          => new OfferElementType(),
                'allow_add'     => true,
                'allow_delete'  => true,
                'by_reference'  => false
            ))
            ->add('deliver',            'entity',       array('class' => 'DyweeShipmentBundle:Deliver', 'property' => 'name'))
            ->add('deliveryCost')
            ->add('save',               'submit')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function configureOptions(OptionsResolverInterface $resolver)
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
        return 'dywee_orderbundle_offer';
    }
}
