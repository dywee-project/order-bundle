<?php

namespace Dywee\OrderBundle\Form;

use Dywee\AddressBundle\Entity\AddressRepository;
use Dywee\AddressBundle\Form\AddressType;
use Dywee\OrderBundle\Entity\BaseOrder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BaseOrderType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $choices = array(
            BaseOrder::STATE_IN_SESSION => BaseOrder::STATE_IN_SESSION,
            BaseOrder::STATE_WAITING => BaseOrder::STATE_WAITING,
            BaseOrder::STATE_IN_PROGRESS => BaseOrder::STATE_IN_PROGRESS,
            BaseOrder::STATE_READY_FOR_SHIPPING => BaseOrder::STATE_READY_FOR_SHIPPING,
            BaseOrder::STATE_IN_SHIPPING => BaseOrder::STATE_IN_SHIPPING,
            BaseOrder::STATE_FINALIZED => BaseOrder::STATE_FINALIZED,
            BaseOrder::STATE_RETURNED => BaseOrder::STATE_RETURNED,
        );
        $builder
            //->add('discountRate')
            //->add('discountValue')
            ->add('description',        TextareaType::class,     array('required' => false))
            ->add('state',              ChoiceType::class,   array(
                'choices' => $choices
            ))
            ->add('billingAddress',     EntityType::class, array(
                'class' => 'DyweeAddressBundle:Address',
                'required' => false,
            ))
            ->add('shippingAddress',    EntityType::class, array(
                'class' => 'DyweeAddressBundle:Address',
                'required' => false,
            ))
            ->add('orderElements',      CollectionType::class,   array(
                'entry_type'          => OrderElementType::class,
                'allow_add'     => true,
                'allow_delete'  => true,
                'by_reference'  => false
            ))
            ->add('orderRentElements',      CollectionType::class,   array(
                'entry_type'          => OrderElementRentType::class,
                'allow_add'     => true,
                'allow_delete'  => true,
                'by_reference'  => false
            ))
            ->add('beginAt',            DateType::class, array('required' => false, 'widget' => 'single_text'))
            ->add('returningAt',            DateType::class, array('required' => false, 'widget' => 'single_text'))
            //->add('deliver',            EntityType::class,       array('class' => 'DyweeShipmentBundle:Deliver', 'property' => 'name'))
            //->add('deliveryInfo',       null,         array('required' => false))
            //->add('deliveryMethod',     ChoiceType::class, array('choices' => array('24R' => 'En point relais', 'HOM' => 'A domicile')))
            //->add('deliveryCost')
            //->add('paymentMethod',     ChoiceType::class, array('choices' => array(1 => 'Liquidité', 2 => 'Virement', 3 => 'Paypal'), 'required' => false))
            //->add('paymentState',      ChoiceType::class, array('choices' => array(0 => 'En attente de paiement', 1 => 'Acompte donné', 2 => 'Payé', 3 => 'Remboursé', 4 => 'Annulé par l\'utilisateur')))
            ->add('save',               SubmitType::class)
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
}
