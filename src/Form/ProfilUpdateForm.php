<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use function Sodium\add;

class ProfilUpdateForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('mail', TextType::class, ['label' =>'Email :'])
        ->add('nom', TextType::class, ['label' =>'Nom :'])
        ->add('prenom', TextType::class, ['label' =>'Prénom :'])
        ->add('telephone', TextType::class, ['label' =>'Téléphone :'])
        ->add('motPasse', TextType::class, ['label' =>'Mot de passe :'])
            ->add('motPasseConf',TextType::class, ['label' =>'Confirmer le mot de passe :'])
            ->add('campus', TextType::class, ['label' =>'Campus :'])
            ->add('photo', FileType::class, ['label' =>'Photo de profil :',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpg',
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                            'image/jfif',
                    ],
                            'mimeTypesMessage' => 'Please upload a valid image',
                    ])
                ],
            ]);
    }
}