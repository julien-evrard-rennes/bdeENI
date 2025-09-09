<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\Models\RechercheSortie;
use App\Form\RechercheSortieType;
use App\Form\SortieAnnulationForm;
use App\Form\SortieDetailsType;
use App\Repository\EtatRepository;
use App\Repository\ParticipantRepository;
use App\Repository\RechercheSortieRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SortieController extends AbstractController
{
    #[Route('', name: 'accueil')]
    public function accueil(RechercheSortieRepository $sortieRepository,
                            Request $request): Response
    {
        $sortie = new RechercheSortie();
        $sortieForm = $this->createForm(RechercheSortieType::class, $sortie);
        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            $critere = $sortieForm->getData();

            $hasCriteria =
                (null !== $critere->getCampus()) ||
                (null !== $critere->getDateHeureDebut()) ||
                (null !== $critere->getDateHeureFin()) ||
                (null !== $critere->getAnciennete()) ||
                ($critere->getOrganisateurPresent() === true) ||
                ($critere->getInscrit() === true) ||
                ($critere->getNonInscrit() === true) ||
                (null !== $critere->getNom() && trim($critere->getNom()) !== '');

            $sorties = $hasCriteria
                ? $sortieRepository->rechercheAvancee($critere, $this->getUser())
                : $sortieRepository->rechercheBasique();
        }
        else {
                $sorties = $sortieRepository->rechercheBasique();
        }
            return $this->render('sortie/accueil.html.twig', [
                'sortieForm' => $sortieForm->createView(),
                'sorties' => $sorties,
            ]);
    }

    #[Route('/creer', name: 'sortie_creer')]
    public function creer(Request $request,
                          EntityManagerInterface $entityManager,
                          EtatRepository $etatRepository,
        ParticipantRepository $participantRepository): Response{

        $sortie = new Sortie();
        $sortieForm = $this->createForm(SortieDetailsType::class, $sortie);
        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {

            $sortie = $sortieForm->getData();
            $sortie->setOrganisateur($this->getUser());
            $sortie->setCampus($participantRepository->find($this->getUser())->getCampus());
            $sortie->setEtat($etatRepository->findOneBy(['libelle' => 'Créée']));
            $publication = $sortieForm->get('publication')->getData();
            if
            ($publication){
                $sortie->setEtat($etatRepository->findOneBy(['libelle' => 'Ouverte']));
            }

            if($sortie->getDateHeureDebut() < new \DateTime()){
                $this->addFlash('error', 'La date de début doit être dans le futur.');
                return $this->redirectToRoute('sortie_creer');
            }

            $entityManager->persist($sortie);
            $entityManager->flush();

            // Ajouter un message flash pour indiquer que la recherche a été effectuée
            $this->addFlash('success', 'La sortie "'.$sortie->getNom().'" a été enregistrée !');

            // Rediriger vers une autre page si nécessaire
            return $this->redirectToRoute('accueil');
        }

        return $this->render('sortie/creer.html.twig',[
            'sortieDetailsForm' => $sortieForm
        ]);

    }

    #[Route('/detail/{id}', name: 'sortie_detail', requirements: ['id' => '\d+'])]
    public function detail(int $id, SortieRepository $sortieRepository): Response{
        $sortie = $sortieRepository->find($id);
        if(!$sortie){
            throw $this->createNotFoundException('La sortie est introuvable !');
        }
        if($sortie->getEtat()->getLibelle() === 'Annulée') {
            return $this->render('sortie/detailannulee.html.twig', [
                'sortie' => $sortie,
            ]);
        }

        return $this->render('sortie/detail.html.twig',[
            'sortie'=> $sortie,
        ]);
    }

    #[Route('/modifier/{id}', name: 'sortie_modifier', requirements: ['id' => '\d+'])]
    public function modifier(int                    $id,
                             SortieRepository       $sortieRepository,
                             EntityManagerInterface $entityManager,
                             Request                $request,
                             EtatRepository $etatRepository,
    ): Response
    {
        $sortie = $sortieRepository->find($id);
        if(!$sortie){
            throw $this->createNotFoundException('La sortie est introuvable !');
        }

        $sortieForm = $this->createForm(SortieDetailsType::class, $sortie);
        $sortieForm->handleRequest($request);

        if($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            $sortie = $sortieForm->getData();
            $publication = $sortieForm->get('publication')->getData();
            if ($publication) {
                $sortie->setEtat($etatRepository->findOneBy(['libelle' => 'Ouverte']));
            }

            if($sortie->getDateHeureDebut() < new \DateTime()){
                $this->addFlash('error', 'La date de début doit être dans le futur.');
                return $this->redirectToRoute('sortie_modifier',['id'=>$id]);
            }

            $entityManager->persist($sortie);
            $entityManager->flush();

            // Ajouter un message flash
            $this->addFlash("success", 'La sortie "'.$sortie->getNom().'" a bien été modifiée.');

            return $this->redirectToRoute('accueil');
        }

        return $this->render('sortie/modifier.html.twig',[
            'sortie'=> $sortie,
            'sortieDetailsForm' => $sortieForm
        ]);
    }

    #[Route('/annuler/{id}', name: 'sortie_annuler', requirements: ['id' => '\d+'])]
    public function annuler(int $id,
                            Request $request,
                            SortieRepository $sortieRepository,
                            EtatRepository $etatRepository,
                            EntityManagerInterface $entityManager): Response{
        $sortie = $sortieRepository->find($id);
        if(!$sortie){
            throw $this->createNotFoundException('La sortie est introuvable !');
        }
        $sortieForm = $this->createForm(SortieAnnulationForm::class, $sortie);
        $sortieForm->handleRequest($request);

        if($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            $sortie = $sortieForm->getData();
            $sortie->setEtat($etatRepository->findOneBy(['libelle' => 'Annulée']));

            $entityManager->persist($sortie);
            $entityManager->flush();

            // Ajouter un message flash
            $this->addFlash("success", 'La sortie "'.$sortie->getNom().'" a bien été annulée.');
            return $this->redirectToRoute('accueil');

        }

        return $this->render('sortie/annulation.html.twig', [
            'sortie'=> $sortie,
            'sortieAnnulationForm' => $sortieForm
        ]);
    }

    #[Route('/inscription/{id}', name: 'sortie_inscription', requirements: ['id' => '\d+'])]
    public function inscription(int $id,
                                SortieRepository $sortieRepository,
                                EntityManagerInterface $entityManager): Response{
        $sortie = $sortieRepository->find($id);
        if(!$sortie){
            throw $this->createNotFoundException('La sortie est introuvable !');
        }

        $user = $this->getUser();
        if ($sortie->getParticipants()->contains($user)) {
            $this->addFlash("warning", 'Vous êtes déjà inscrit à la sortie "'.$sortie->getNom().'.');
        } else {
            $sortie->setParticipants($user);
            $entityManager->persist($sortie);
            $entityManager->flush();
            $this->addFlash("success", 'Vous êtes inscrit à la sortie "'.$sortie->getNom().'.');
        }

        $sorties = $sortieRepository ->findBy([],['dateHeureDebut' => 'DESC'], 10);

        return $this->redirectToRoute('accueil', [
            'sorties'=> $sorties,
        ]);
    }

    #[Route('/desinscription/{id}', name: 'sortie_desinscription', requirements: ['id' => '\d+'])]
    public function desinscription(int $id,
                                   SortieRepository $sortieRepository,
                                   EntityManagerInterface $entityManager): Response
    {
        $sortie = $sortieRepository->find($id);
        if (!$sortie) {
            throw $this->createNotFoundException('La sortie est introuvable !');
        }

        $user = $this->getUser();
        if ($sortie->getParticipants()->contains($user)) {
            $sortie->removeParticipant($user);
            $entityManager->persist($sortie);
            $entityManager->flush();
        }

        $this->addFlash("success", 'Vous avez été désinscrit de"'.$sortie->getNom().'.');

        return $this->redirectToRoute('accueil');

    }

    #[Route('/supprimer/{id}', name: 'sortie_supprimer', requirements: ['id' => '\d+'])]
    public function supprimerSortie(int $id,
                              SortieRepository $sortieRepository,
                              EntityManagerInterface $entityManager
    ): Response
    {
        $sortie = $sortieRepository->find($id);
        if (!$sortie) {
            throw $this->createNotFoundException('La sortie est introuvable !');
        }

        $entityManager->remove($sortie);
        $entityManager->flush();

        $this->addFlash("success", 'La sortie "' . $sortie->getNom() . '" a bien été supprimée.');

        return $this->redirectToRoute('accueil');

    }



}