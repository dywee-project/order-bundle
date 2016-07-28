<?php

namespace Dywee\OrderBundle\Form;

use Misd\PhoneNumberBundle\Form\Type\PhoneNumberType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Dywee\AddressBundle\Form\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BillingAddressType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //TODO remettre en place les emails
        $builder
            ->add('company',    TextType::class, array('required' => false))
            ->add('firstName',      TextType::class)
            ->add('lastName',       TextType::class)
            /*->add('email',          RepeatedType::class, array(
                    'type' => EmailType::class,
                    'invalid_message' => 'Les mots de passe doivent correspondre',
                    'options' => array('required' => true),
                    'first_options'  => array('label' => 'Adresse e-mail'),
                    'second_options' => array('label' => 'Confirmer Adresse e-mail')
                )
            )*/
            ->add('line1',          TextType::class)
            ->add('line2',          TextType::class, array('required' => false))
            //->add('mobile',         PhoneNumberType::class)
            //->add('zip',            TextType::class)
            ->add('city',           EntityType::class,          array(
                'class'         => 'DyweeAddressBundle:City',
                'choice_label'  => 'zipName',
            ))
            /*->add('country',        EntityType::class,          array(
                'class'         => 'DyweeAddressBundle:Country',
                'choice_label'  => 'name',
            ))*/
            ->add('save',      SubmitType::class)
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Dywee\AddressBundle\Entity\Address'
        ));
    }
}
