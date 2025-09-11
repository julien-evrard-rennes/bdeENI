<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\LessThan;
use Symfony\Component\Validator\Constraints\NotBlank;

class SortieDetailsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom',null,[
                'label' => 'Nom * :',
                'constraints' => [
                    new NotBlank(message: 'Veuillez saisir un nom.'),
                ],

            ])
            ->add('dateHeureDebut', DateTimeType::class, [
                'property_path' => 'dateHeureDebut',
                'widget' => 'single_text',
                'label' => 'Date et heure de la sortie* :',
                'html5' => true,
                'constraints' => [
                    new NotBlank(message: 'Veuillez entrer une date'),
                    new GreaterThanOrEqual(['dateLimiteInscription',
                    'message ' => 'La date de sortie doit être postérieure à  la date limite d\'inscription']),
                    new GreaterThan(['now',
                        'message ' => 'La date de sortie doit être postérieure à aujourdh\'hui'])
                    ],
            ])

            ->add('duree',null,[
                'required' => false,
                'label' => 'Durée (en minutes) :'
            ])
            ->add('dateLimiteInscription', DateType::class, [
                'property_path' => 'dateLimiteInscription',
                'widget' => 'single_text',
                'label' => 'Date limite de l\'inscription* :',
                'constraints' => [
                    new NotBlank(message: 'Veuillez entrer une date'),
                    new LessThan([
                        'propertyPath' => 'parent.all[dateHeureDebut].data',
                        'message' => 'La date limite d\'inscription doit être avant le jour de la sortie'
                    ]),
                    new GreaterThanOrEqual(['now',
                        'message ' => 'La date limite d\'inscription ne peut être dans le passé']),
                ],
            ])

            ->add('nbInscriptionsMax', null,[
                'label' => 'Nombre de places * :',
                'constraints' => [
                    new Length(max: 3, maxMessage: 'Pas plus de 999 participants'),
                ],
            ])
            ->add('infosSortie', null,[
                'required' => false,
                'label' => 'Description et infos :'
            ])
            ->add('lieu', EntityType::class, [
                'label' => 'Lieu de la sortie :',
                'class' => Lieu::class,
                'choice_label' => 'nom',
                'required' => false,
            ])
            ->add('publication', checkboxType::class, [
                'property_path' => 'etat',
                'label' => 'Publier la sortie',
                'required' => false,
                'mapped' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
