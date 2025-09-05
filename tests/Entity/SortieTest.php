<?php

use App\Entity\Sortie;
use App\Entity\Participant;
use PHPUnit\Framework\TestCase;

class SortieTest extends TestCase
{
    public function testEtatAssignement()
    {
        $sortie = new Sortie();
        $sortie->setNom('test');
        $sortie->setInfosSortie('Pique nique au cocoBongo');
        $sortie->setNbInscriptionsMax(10);
        $sortie->setDuree(120);
        $sortie->setDateHeureDebut(new DateTime('2024-06-30 14:00:00'));
        $sortie->setDateLimiteInscription(new DateTime('2024-06-25 23:59:59'));
        $Part1 = new Participant();
        $Part2 = new Participant();
        $Part1->setNom('Johnson');
        $Part1->setPrenom('Alice');
        $Part2->setNom("Smith");
        $Part2->setPrenom("Bob");
        $sortie->setParticipants($Part1);
        $sortie->setParticipants($Part2);
        $participants = $sortie->getParticipants();
        self::assertCount(2, $participants);
        self::assertTrue($participants->contains($Part1));
        self::assertTrue($participants->contains($Part2));
         foreach ($participants as $p) {
             echo $p->getPrenom().' '.$p->getNom().PHP_EOL;
         }
         echo $sortie->__toString();


    }
}
