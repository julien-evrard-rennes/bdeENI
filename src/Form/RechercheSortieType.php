<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RechercheSortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'nom',
            ])
            ->add('nom', SearchType::class, [
                'required' => false,
                'label' => 'Le nom de la sortie contient :'
            ])
            ->add('dateHeureDebut',DateType::class,[
                'property_path' => 'dateHeureDebut',
                'required' => false,
                'widget'=>'single_text',
                'label' => 'Entre',
                'data' => new \DateTime('now')
            ])
            ->add('dateHeureFin', DateType::class,[
                'property_path' => 'dateHeureDebut',
                'required' => false,
                'widget'=>'single_text',
                'label' => 'et',
                'data' => new \DateTime('+ 15 days')
            ])
            /**->add('etat', null, [
                'required' => false,
                'label' => 'sorties passées',
            ]) **/
            /**->add('organisateur', EntityType::class, [
                'class' => Participant::class,
                'choice_label' => 'id',
            ])
            ->add('participants', EntityType::class, [
                'class' => Participant::class,
                'choice_label' => 'id',
                'multiple' => true,
            ]) **/
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
