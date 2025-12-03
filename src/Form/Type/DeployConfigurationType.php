<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class DeployConfigurationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('repositoryOwner', TextType::class)
            ->add('repositoryName', TextType::class)
            ->add('deployPath', TextType::class)
            ->add('save', SubmitType::class, [
                'attr' => [
                    'class' => 'button',
                ]
            ]);
    }
}
