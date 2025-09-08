<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Form\LieuxAjoutForm;
use App\Form\ProfilUpdateForm;
use App\Repository\CampusRepository;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class MainController extends AbstractController
{
    #[Route('/AjoutLieu', name: 'ajoutLieu')]
    public function ajoutLieu(Request $request,
                              EntityManagerInterface $entityManager): Response
    {
        $lieu = new Lieu();
        $form = $this->createForm(LieuxAjoutForm::class, $lieu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($lieu);
            $entityManager->flush();

            $this->addFlash('success', 'Lieu ajouté avec succès !');

            return $this->redirectToRoute('sortie_creer' );

        }
        return $this->render("lieu/ajoutLieu.html.twig", [
            'form' => $form->createView()
        ]);
    }

    #[Route('/Profil/{id}', name: 'profil', requirements: ['id' => '\d+'])]
    public function profil(int $id, ParticipantRepository $participantRepository): Response
    {
        $participant = $participantRepository->find($id);

        return $this->render("profil/profil.html.twig", [
            'participant' => $participant
        ]);
    }
    #[Route('/ProfilUpdate', name: 'profilUpdate')]
    public function profilUpdate(Request $request): Response
    {
        $participant = $this->getUser();
        if (!$participant) {
            throw $this->createAccessDeniedException();
        }
        return $this->redirectToRoute('update', ['id' => $participant->getId()]);
    }
    #[Route('/ProfilUpdate/{id}', name: 'update', requirements: ['id' => '\d+'])]
    public function update(
        int $id,
        ParticipantRepository $participantRepository,
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $hasher
    ): Response {
        $participant = $participantRepository->find($id);
        if (!$participant) {
            throw $this->createNotFoundException('Participant introuvable.');
        }

        $form = $this->createForm(ProfilUpdateForm::class, $participant, [
            // Assure que le POST arrive bien sur cette route avec l'id
            'action' => $this->generateUrl('update', ['id' => $participant->getId()]),
            'method' => 'POST',
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();
            if (!empty($plainPassword)) {
                $hashed = $hasher->hashPassword($participant, $plainPassword);
                $participant->setMotPasse($hashed);
            }
        $image = $form->get('photo')->getData();

        /**
         * @var UploadedFile $image
         */
        $newFileName = uniqid() . '.' . $image->guessExtension();
        $image->move($this->getParameter('photo_directory'), $newFileName);

        $participant->setPhoto($newFileName);

            // L'entité est déjà gérée, flush suffit
            $entityManager->persist($participant);
            $entityManager->flush();

            $this->addFlash('success', 'Participant mis à jour !');

            // PRG: redirection après succès
            return $this->redirectToRoute('update', ['id' => $participant->getId()]);
        }

        // GET initial ou formulaire invalide => on affiche
        return $this->render('profil/profilUpdate.html.twig', [
            'form' => $form->createView(),
            'participant' => $participant,
        ]);
    }


}



