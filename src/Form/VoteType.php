<?php

namespace App\Form;

use App\Entity\Topic;
use App\Entity\User;
use App\Entity\Vote;
use App\Repository\TopicRepository;
use App\Repository\UserRepository;
use App\Utils\Enum\VoteChoice;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VoteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('choice', ChoiceType::class, [
                'choices' => [VoteChoice::POSITIVE, VoteChoice::NEGATIVE],
                'documentation' => [
                    'type' => 'string',
                    'description' => 'Member vote (Sim/Não).',
                    'example' => VoteChoice::NEGATIVE,
                ]
            ])
            ->add('topic_uuid', EntityType::class, [
                'property_path' => 'topic',
                'class' => Topic::class,
                'choice_value' => 'uuid',
                'query_builder' => function (TopicRepository $repository) {
                    return $repository->newCriteriaActiveQb();
                },
                'documentation' => [
                    'type' => 'string',
                    'description' => 'Cooperative to be associated to.',
                    'example' => 'uuid-uuid-uuid-uuid',
                ]
            ])
            ->add('user_uuid', EntityType::class, [
                'property_path' => 'user',
                'class' => User::class,
                'choice_value' => 'uuid',
                'query_builder' => function (UserRepository $repository) {
                    return $repository->newCriteriaActiveQb();
                },
                'documentation' => [
                    'type' => 'string',
                    'description' => 'Voting member.',
                    'example' => 'uuid-uuid-uuid-uuid',
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Vote::class
        ]);
    }
}
