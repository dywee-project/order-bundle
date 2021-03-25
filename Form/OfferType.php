<?php

namespace Dywee\OrderBundle\Form;

use Dywee\AddressBundle\Form\AddressType;
use Dywee\OrderBundle\Entity\Deliver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
            ->add('description', TextareaType::class, array('required' => false))
            ->add('state', ChoiceType::class, array('choices' => array(0 => 'Annulée', 1 => 'Proposée', 2 => 'Validée')))
            ->add('address', EntityType::class, array('class' => 'DyweeAddressBundle:Address', 'property' => 'formValue'))
            ->add('offerElements', CollectionType::class, array(
                'entry_type'        => OfferElementType::class,
                'allow_add'         => true,
                'allow_delete'      => true,
                'by_reference'      => false
            ))
            ->add('deliver', 'entity', array('class' => Deliver::class, 'property' => 'name'))
            ->add('deliveryCost')
            ->add('save', 'submit')
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Dywee\OrderBundle\Entity\Offer'
        ));
    }
}
