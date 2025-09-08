<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Participant;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class CreerUtilisateurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('Mail', EmailType::class, [
                'label' => 'Email :',
                'trim' => true,
                'constraints' => [
                    new NotBlank(message: 'Veuillez saisir un email.'),
                    new Email(message: 'Email invalide.'),
                ],
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom :',
                'trim' => true,
                'constraints' => [
                    new NotBlank(message: 'Veuillez saisir un nom.'),
                    new Length(max: 180),
                ],
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom :',
                'trim' => true,
                'constraints' => [
                    new NotBlank(message: 'Veuillez saisir un prénom.'),
                    new Length(max: 180),
                ],
            ])
            ->add('telephone', TelType::class, [
                'label' => 'Téléphone :',
                'trim' => true,
                'required' => false,
                'constraints' => [
                    new Length(max: 30),
                    // À adapter à votre format
                    // new Assert\Regex(pattern: '/^\+?[0-9 .\-()]{7,}$/', message: 'Téléphone invalide.'),
                ],
            ])
            // Mot de passe en double (non mappé) pour la confirmation
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'required' => false, // Laisser vide si on ne change pas le MDP
                'first_options'  => ['label' => 'Mot de passe :'],
                'second_options' => ['label' => 'Confirmer le mot de passe :'],
                'invalid_message' => 'Les mots de passe ne correspondent pas.',
                'constraints' => [
                    // S’applique uniquement si non vide
                    new Length(min: 8, minMessage: 'Au moins 8 caractères.'),
                ],
            ])
            ->add('campus', EntityType::class, [
                'label' => 'Campus :',
                'class' => Campus::class,
                'choice_label' => 'nom',
                'placeholder' => 'Sélectionnez un campus',
                'required' => true,
            ])
            ->add('photo', FileType::class, [
                'label' => 'Photo de profil :',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new Image(
                        maxSize: '1M',
                        maxSizeMessage: "L'image ne doit pas dépasser 1 Mo",
                        extensions: ['png', 'jpg'],
                        extensionsMessage: "Les types autorisés sont .png et .jpg"
                    )],
            ])

        ->add('role', ChoiceType::class, [
        'expanded'=>false,
        'multiple'=>false,
        'label' => 'Rôle :',
        'required' => true,
        'choices' => [
            'ROLE_USER' => 'ROLE_USER',
            'ROLE_ADMIN' =>'ROLE_ADMIN'
        ],
            ])
            ->add('actif', ChoiceType::class, [
                'expanded'=>false,
                'multiple'=>false,
                'label' => 'Actif :',
                'required' => true,
                'choices' => [
                    'Oui' => true,
                    'Non' => false
                ],

            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
