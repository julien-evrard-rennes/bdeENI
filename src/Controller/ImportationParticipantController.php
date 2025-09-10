<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ImportCsvType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class ImportationParticipantController extends AbstractController
{
    #[Route('/importer-participant', name: 'app_import_participant')]
    public function index(
        Request                     $request,
        EntityManagerInterface      $entityManager,
        UserPasswordHasherInterface $passwordHasher,
    ): Response
    {
        $form = $this->createForm(ImportCsvType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $csvFile = $form->get('csvFile')->getData();

            // Vérification que le fichier a bien été uploadé
            if ($csvFile) {
                try {
                    if (($handle = fopen($csvFile->getRealPath(), "r")) !== false) {
                        // Ignorer l'en-tête
                        fgetcsv($handle);

                        while (($data = fgetcsv($handle, null, ";")) !== FALSE) {
                            $participant = new Participant();
                            $participant->setPseudo($data[0]);
                            $participant->setNom($data[1]);
                            $participant->setPrenom($data[2]);
                            $participant->setMail($data[3]);
                            $participant->setTelephone((int)$data[4]);

                            $hashedPassword = $passwordHasher->hashPassword($participant, ($data[1] . '-' . $data[2]));
                            $participant->setMotPasse($hashedPassword);

                            $participant->setRole('ROLE_USER');
                            $participant->setActif(true);

                            $entityManager->persist($participant);
                        }

                        $entityManager->flush();
                        fclose($handle);

                        $this->addFlash('success', 'Import réalisé avec succès');
                        return $this->redirectToRoute('utilisateur_creer');

                    }
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Une erreur est survenue lors de l\'import');
                }
            }
        }
        return $this->render('admin/importer_utilisateur.html.twig', [
            'form' => $form
        ]);

    }
}