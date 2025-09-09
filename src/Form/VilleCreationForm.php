<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class VilleCreationForm extends AbstractType
{
    public function BuildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de la ville :',
                'trim' => true,
            ])
            ->add('codePostal', TextType::class, [
                'label' => 'Code Postal :',
                'trim' => true,
            ]);

    }
}