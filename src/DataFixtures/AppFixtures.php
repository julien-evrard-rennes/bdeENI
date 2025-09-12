<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Entity\Ville;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use function Symfony\Component\Clock\now;


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
     $Etats = ['Créée','Ouverte','Clôturée','Activité en cours','Passée','Annulée','Historisée'];

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
            echo $psw . "\n";
            $hashed = $this->passwordHasher->hashPassword($participant, $psw);

            $participant->setMotPasse($hashed);
            $participant->setRoles(['ROLE_USER']);
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
        $SortieNames = ['Randonnée en montagne', 'Visite de musée', 'Atelier de cuisine', 'Soirée karaoké', 'Tournoi de poker', 'Journée plage', 'Excursion en bateau', 'Séance de yoga', 'Balade à vélo', 'Pique-nique au parc', 'Chasse au trésor', 'Atelier peinture', 'Cours de danse', 'Soirée cinéma en plein air', 'Dégustation de vins', 'Atelier poterie', 'Randonnée urbaine', 'Visite de château', 'Atelier photographie', 'Soirée jeux de société', 'Sortie au bar'];
        $SortieDesc = ['Teambuilding', 'Sortie conviviale entre amis', 'Découverte culturelle', 'Activité sportive', 'Moment de détente', 'Exploration urbaine', 'Aventure en plein air', 'Atelier créatif', 'Soirée festive', 'Rencontre gastronomique'];

        $faker = Factory::create('fr_FR');
        for ($i = 0; $i < 40; $i++) {
            $sortie = new Sortie();
            $sortie->setCampus($faker->randomElement($campus));
            $sortie->setNom($faker->randomElement($SortieNames));
            $sortie->setLieu($faker->randomElement($lieux));
            $sortie->setDuree($faker->numberBetween(60, 300));
            $sortie->setDateHeureDebut($faker->dateTimeBetween('-2 month', '+2 month'));
            $sortie->setDateLimiteInscription($faker->dateTimeBetween('-2 month', $sortie->getDateHeureDebut()));
            if ($sortie->getDateLimiteInscription() < now() && $sortie->getDateHeureDebut() > now()) {
                $sortie->setEtat($manager->getRepository(Etat::class)->findOneBy(['libelle' => 'Cloturée']));
            } elseif ($sortie->getDateHeureDebut() < now()->modify('+1 month')) {
                $sortie->setEtat($manager->getRepository(Etat::class)->findOneBy(['libelle' => 'Historisée']));
            } elseif ($sortie->getDateHeureDebut() < (now()->modify('-2 hours'))) {
                $sortie->setEtat($manager->getRepository(Etat::class)->findOneBy(['libelle' => 'Passée']));
            } elseif ($sortie->getDateLimiteInscription() >= now() && $sortie->getDateHeureDebut() > now()) {
                $sortie->setEtat($manager->getRepository(Etat::class)->findOneBy(['libelle' => 'Ouverte']));
            } elseif ($sortie->getDateHeureDebut() <= now() && $sortie->getDateHeureDebut() >= (new \DateTime())->modify('-2 hours')) {
                $sortie->setEtat($manager->getRepository(Etat::class)->findOneBy(['libelle' => 'Activité en cours']));
            } else {
                $sortie->setEtat($faker->randomElement($Etat));
            }
            $sortie->setInfosSortie($faker->randomElement($SortieDesc));
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
        $faker = Factory::create('fr_FR');
        $campus = $manager->getRepository(Campus::class)->findAll();
        $participant = new participant();
        $participant->setMail("julien.evrard2025@campus-eni.fr");
        $roles[] = 'ROLE_USER';
        $participant->setRoles($roles);
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
        $participant = new participant();
        $participant->setMail("trotin.kelan2025@campus-eni.fr");

        $participant->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
        $participant->setActif(true);
        $participant->setCampus($campus[0]);
        $participant->setNom("Trotin");
        $participant->setPrenom("Kelan");
        $participant->setTelephone(0707070707);

        $psw = "Kelan2025!";
        $hashed = $this->passwordHasher->hashPassword($participant, $psw);
        $participant->setMotPasse($hashed);
        $manager->persist($participant);
        $manager->flush();
    }
}
