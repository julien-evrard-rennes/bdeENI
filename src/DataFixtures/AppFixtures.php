<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Roles;
use App\Entity\Sortie;
use App\Entity\Ville;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher) {}
    public function load(ObjectManager $manager): void
    {
        $this->addCampus($manager);
        $this->addEtats($manager);
        $this->addParticipants($manager);
        $this->addVilles($manager);
        $this->addLieux($manager);
        $this->addSorties($manager);
        $this->addListeParticipants($manager);
        $this->addPerso($manager);

    }
    private function addCampus(ObjectManager $manager): void
    {
        $campus = ['Chartres-de-bretagne','Saint-Herblain','Niort','Quimper'];

        foreach ($campus as $cam) {
            $campus = new Campus();
            $campus->setNom($cam);
            $manager->persist($campus);
        }
        $manager->flush();
    }
    private function addEtats(ObjectManager $manager): void
    {
     $Etats = ['Créée','Ouverte','Clôturée','Activité en cours','Passée','Annulée'];

        foreach ($Etats as $eta) {
            $etat = new Etat();
            $etat->setLibelle($eta);

            $manager->persist($etat);
        }
        $manager->flush();
    }
    private function addParticipants(ObjectManager $manager): void
    {
        $campus = $manager->getRepository(Campus::class)->findAll();

        $faker = Factory::create('fr_FR');
        for ($i = 0; $i < 20; $i++) {
            $participant = new participant();
            $participant->setCampus($faker->randomElement($campus));
            $participant->setNom($faker->lastName());
            $participant->setPrenom($faker->firstName());
            $participant->setTelephone(intval($faker->phoneNumber()));;
            $participant->setMail($faker->email());
            $participant->setActif($faker->boolean(80));

            $psw = $faker->password();
            $hashed = $this->passwordHasher->hashPassword($participant, $psw);

            $participant->setMotPasse($hashed);
            $participant->setRole($faker->randomElement(['ROLE_USER','ROLE_ADMIN']));
            $manager->persist($participant);
        }
        $manager->flush();
    }
    private function addVilles(ObjectManager $manager) : void
    {
        $faker = Factory::create('fr_FR');
        for ($i = 0; $i < 20; $i++) {
            $ville = new Ville();
            $ville->setNom($faker->city());
            $ville->setCodePostal($faker->postcode());
            $manager->persist($ville);

        }
        $manager->flush();
    }
    private function addLieux(ObjectManager $manager) : void
    {
        $Ville = $manager->getRepository(Ville::class)->findAll();

        $faker = Factory::create('fr_FR');
        for ($i = 0; $i < 20; $i++) {
            $lieu = new Lieu();
            $lieu->setVille($faker->randomElement($Ville));
            $lieu->setNom($faker->company());
            $lieu->setRue($faker->streetAddress());
            $lieu->setLatitude($faker->latitude());
            $lieu->setLongitude($faker->longitude());

            $manager->persist($lieu);
        }
        $manager->flush();
    }
    private function addSorties(ObjectManager $manager): void
    {
        $Etat = $manager->getRepository(Etat::class)->findAll();
        $campus = $manager->getRepository(Campus::class)->findAll();
        $participants = $manager->getRepository(Participant::class)->findAll();
        $lieux = $manager->getRepository(Lieu::class)->findAll();

        $faker = Factory::create('fr_FR');
        for ($i = 0; $i < 20; $i++) {
            $sortie = new Sortie();
            $sortie->setCampus($faker->randomElement($campus));
            $sortie->setEtat($faker->randomElement($Etat));
            $sortie->setNom($faker->realText(20));
            $sortie->setLieu($faker->randomElement($lieux));
            $sortie->setDurée($faker->numberBetween(60, 300));
            $sortie->setDateLimiteInscription($faker->dateTimeBetween('-1 month', '+1 month'));
            $sortie->setDateHeureDebut($faker->dateTimeBetween('-1 month', '+1 month'));
            $sortie->setInfosSortie($faker->realText(100));
            $sortie->setNbInscriptionsMax($faker->numberBetween(5, 20));
            $sortie->setOrganisateur($faker->randomElement($participants));

            $manager->persist($sortie);
        }
        $manager->flush();
    }
    private function addListeParticipants(ObjectManager $manager): void
    {
        $sorties = $manager->getRepository(Sortie::class)->findAll();
        $participants = $manager->getRepository(Participant::class)->findAll();

        $faker = Factory::create('fr_FR');

        foreach ($sorties as $sortie) { // Correction de $Sorties en $sorties
            $nbParticipants = $faker->numberBetween(1, $sortie->getNbInscriptionsMax());
            $selectedParticipants = $faker->randomElements($participants, $nbParticipants);

            foreach ($selectedParticipants as $participant) {
                $sortie->setParticipants($participant);
            }
            $manager->persist($sortie);
        }
        $manager->flush();
    }

    private function addPerso(ObjectManager $manager): void
    {
        $campus = $manager->getRepository(Campus::class)->findAll();

        $participant = new participant();
        $participant->setMail("julien.evrard2025@campus-eni.fr");
        $participant->setRole("ROLE_ADMIN");
        $participant->setActif(true);
        $participant->setCampus($campus[0]);
        $participant->setNom("Evrard");
        $participant->setPrenom("Julien");
        $participant->setTelephone(0606060606);

        $psw = "Julien2025!";
        $hashed = $this->passwordHasher->hashPassword($participant, $psw);

        $participant->setMotPasse($hashed);

        $manager->persist($participant);
        $manager->flush();
    }
}
