<?php

namespace Dywee\OrderBundle\Form;

use Dywee\OrderBundle\Entity\Shipment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ShipmentType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $choices = array(
            Shipment::STATE_NOT_PREPARED => Shipment::STATE_NOT_PREPARED,
            Shipment::STATE_WAITING => Shipment::STATE_WAITING,
            Shipment::STATE_PREPARING => Shipment::STATE_PREPARING,
            Shipment::STATE_SHIPPING => Shipment::STATE_SHIPPING,
            Shipment::STATE_SHIPPED => Shipment::STATE_SHIPPED,
            Shipment::STATE_WAITING_CUSTOMER => Shipment::STATE_WAITING_CUSTOMER,
            Shipment::STATE_RETURNED => Shipment::STATE_RETURNED,

        );
        $builder
            ->add('state',      ChoiceType::class, array('choices' => $choices))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Dywee\OrderBundle\Entity\Shipment'
        ));
    }
}
