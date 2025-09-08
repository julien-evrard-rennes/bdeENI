<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class AdminController extends AbstractController
{
    #[Route('/utilisateurs', name: 'utilisateurs')]
    public function utilisateurs(ParticipantRepository $participantRepository)
    {
         $participants     = $participantRepository->findAll();
        return $this->render("admin/utilisateurs.html.twig", [
            'participants' => $participants
        ]);
    }

    #[Route('/Profil/creer', name: 'utilisateur_creer')]
    public function creerUtilisateur(
        ParticipantRepository $participantRepository,
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $userPasswordHasher
    ) {
        $participant = new Participant();
        $form = $this->createForm(\App\Form\CreerUtilisateurType::class);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();
            if (!empty($plainPassword)) {
                $hashedPassword = $userPasswordHasher->hashPassword($form->getData(), $plainPassword);
                $form->getData()->setMotPasse($hashedPassword);
            }
            $participant = $form->getData();

            $image = $form->get('photo')->getData();
            /**
             * @var UploadedFile $image
             */
            $newFileName = uniqid() . '.' . $image->guessExtension();
            $image->move($this->getParameter('photo_directory'), $newFileName);

            $participant->setPhoto($newFileName);

            $entityManager->persist($participant);
            $entityManager->flush();

            $this->addFlash('success', 'Utilisateur créé avec succès !');

            return $this->render("profil/profil.html.twig", [
                'participant' => $participant
            ]);}

        return $this->render("admin/creer_utilisateur.html.twig",[
            'form' => $form->createView(),
        ]);
    }
    #[Route('/admin/supprimer/{id}', name: 'supprimer', requirements: ['id' => '\d+'])]
    public function supprimer(int $id, EntityManagerInterface $entityManager,ParticipantRepository $participantRepository)
    {
        $participant = $participantRepository->find($id);
        if ($participant) {
            $entityManager->remove($participant);
            $entityManager->flush();
            $this->addFlash('success', 'Utilisateur supprimé avec succès !');
            return $this->redirectToRoute('utilisateurs');
        }
    }
    #[Route('/desactiver/{id}', name: 'desactiver', requirements: ['id' => '\d+'])]
    public function desactiver(int $id, EntityManagerInterface $entityManager,ParticipantRepository $participantRepository)
    {
        $participant = $participantRepository->find($id);
        if ($participant) {
            $participant->setActif(false);
            $entityManager->flush();
            $this->addFlash('success', 'Utilisateur désactivé avec succès !');
            return $this->redirectToRoute('utilisateurs');
        }
    }
    #[Route('/activer/{id}', name: 'activer', requirements: ['id' => '\d+'])]
    public function activer(int $id, ParticipantRepository $participantRepository, EntityManagerInterface $entityManager)
    {
        $participant = $participantRepository->find($id);
        if ($participant) {
            $participant->setActif(true);
            $entityManager->flush();
            $this->addFlash('success', 'Utilisateur activé avec succès !');
            return $this->redirectToRoute('utilisateurs');
        }
    }


}