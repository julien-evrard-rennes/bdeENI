<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieDetailsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom',null,[
                'label' => 'Nom * :'
            ])
            ->add('campus', EntityType::class, [
                'label' => 'Campus :',
                'class' => Campus::class,
                'choice_label' => 'nom',
                'disabled' => true,
            ])
            ->add('dateHeureDebut', DateTimeType::class, [
                'property_path' => 'dateHeureDebut',
                'widget' => 'single_text',
                'label' => 'Date et heure de la sortie* :',
                'html5' => true,
            ])

            ->add('duree',null,[
                'required' => false,
                'label' => 'Durée (en minutes) :'
            ])
            ->add('dateLimiteInscription', DateType::class, [
                'property_path' => 'dateLimiteInscription',
                'widget' => 'single_text',
                'label' => 'Date limite de l\'inscription* :',
            ])

            ->add('nbInscriptionsMax', null,[
                'label' => 'Nombre de places * :'
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
