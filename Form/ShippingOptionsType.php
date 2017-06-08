<?php

namespace Dywee\OrderBundle\Form;

use Dywee\OrderBundle\Entity\BaseOrderInterface;
use Dywee\OrderBundle\Service\ShippingMethodHandler as ShippingMethodHandler;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ShippingOptionsType extends AbstractType
{
    /** @var ShippingMethodHandler */
    private $shippingMethodHandler;

    /**
     * ShippingOptionsType constructor.
     *
     * @param ShippingMethodHandler $shippingMethod
     */
    public function __construct(ShippingMethodHandler $shippingMethod)
    {
        $this->shippingMethodHandler = $shippingMethod;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('shippingMethod', ChoiceType::class, [
            'choices'  => $this->shippingMethodHandler->calculateForOrder(),
            'label'    => 'checkout.shipping_method',
            'expanded' => true
        ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
    }
}
