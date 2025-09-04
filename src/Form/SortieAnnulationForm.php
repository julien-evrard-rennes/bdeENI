<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class SortieAnnulationForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
{
    $builder
        ->add('infosSortie', TextType::class, [
            'label' => 'Raison de l\'annulation :',
            'required' => true,
        ]);
}
}