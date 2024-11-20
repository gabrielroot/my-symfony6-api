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

class CooperativeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'documentation' => [
                    'type' => 'string',
                    'description' => 'The cooperative name.',
                    'example' => 'Sicred S/A',
                ]
            ])
            ->add('fantasyName', TextType::class, [
                'documentation' => [
                    'type' => 'string',
                    'description' => 'The cooperative fantasy name.',
                    'example' => 'Sicred',
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Cooperative::class
        ]);
    }
}
