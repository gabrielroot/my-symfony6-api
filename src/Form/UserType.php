<?php

namespace App\Form;

use App\Entity\Cooperative;
use App\Entity\User;
use App\Repository\CooperativeRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'documentation' => [
                    'type' => 'string',
                    'description' => 'The user unique username.',
                    'example' => 'john123',
                ]
            ])
            ->add('name', TextType::class, [
                'documentation' => [
                    'type' => 'string',
                    'description' => 'The user name.',
                    'example' => 'john',
                ]
            ])
            ->add('cooperative_uuid', EntityType::class, [
                'property_path' => 'cooperative',
                'class' => Cooperative::class,
                'choice_value' => 'uuid',
                'query_builder' => function (CooperativeRepository $repository) {
                    return $repository->newCriteriaActiveQb();
                },
                'documentation' => [
                    'type' => 'string',
                    'description' => 'Cooperative to be associated to.',
                    'example' => 'uuid-uuid-uuid-uuid',
                ]
            ])
            ->add('address', AddressType::class, [])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class
        ]);
    }
}
