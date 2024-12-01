<?php

namespace App\Form;

use App\Entity\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('zipCode', TextType::class, [
                'documentation' => [
                    'type' => 'string',
                    'description' => 'The user unique username.',
                    'example' => 'john123',
                ]
            ])
            ->add('street', TextType::class, [
                'documentation' => [
                    'type' => 'string',
                    'description' => 'The user name.',
                    'example' => 'john',
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Address::class
        ]);
    }
}
