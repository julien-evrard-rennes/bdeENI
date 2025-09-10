<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;

class ImportCsvType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('csvFile', FileType::class, [
                'label' => 'Entrez votre fichier CSV',
                'constraints' => [
                    new File([
                        'mimeTypes' => ['text/csv', 'text/plain'],
                        'mimeTypesMessage' => 'Veuillez charger un fichier CSV valide',
                    ])
                ],
            ]);
    }
}
