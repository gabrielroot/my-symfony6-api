<?php

namespace App\Form;

use App\Entity\Cooperative;
use App\Entity\Topic;
use App\Repository\CooperativeRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TopicType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('description', TextType::class, [
                'documentation' => [
                    'type' => 'string',
                    'description' => 'A detailed description about the topic.',
                    'example' => 'Vote for a new president. The last one was pretty bad.',
                ]
            ])
            ->add('title', TextType::class, [
                'documentation' => [
                    'type' => 'string',
                    'description' => 'The topic name.',
                    'example' => 'Vote for a new president.',
                ]
            ])
            ->add('closeTime', DateTimeType::class, [
                'widget' => 'single_text',
                'documentation' => [
                    'type' => 'datetime',
                    'description' => 'The topic expiration time.',
                    'example' => '2024-11-20T16:03:00',
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
                    'example' => 'uuid-uuid-uuid',
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Topic::class
        ]);
    }
}
