<?php

namespace App\Controller;

use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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