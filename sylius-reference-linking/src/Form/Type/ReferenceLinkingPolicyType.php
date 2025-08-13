<?php

namespace Sylius\ReferenceLinking\Form\Type;

use Sylius\ReferenceLinking\Entity\ReferenceLinkingPolicy;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ReferenceLinkingPolicyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('referenceType', HiddenType::class)
            ->add('targetLevel', ChoiceType::class, [
                'choices' => [
                    'Variationsgruppe' => ReferenceLinkingPolicy::TARGET_GROUP,
                    'Variante' => ReferenceLinkingPolicy::TARGET_VARIANT,
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ReferenceLinkingPolicy::class,
        ]);
    }
}

