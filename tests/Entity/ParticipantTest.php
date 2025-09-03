<?php

use App\Entity\Roles;
use App\Entity\Sortie;
use App\Entity\Participant;
use PHPUnit\Framework\TestCase;
Class ParticipantTest extends TestCase
{


    public function testParticipant()
    {
        $participant = new Participant();
        $participant->setEmail('Edouarde@mailbidon.com');
        $participant->setPassword('azerty');
        $participant->setPrenom('Edouarde');
        $participant->setNom('Dupont');
        $participant->setTelephone('0606060606');
        $participant->setActif(true);
        $roles = new Roles();
        $roles->setLibelle('ROLE_USER');
        $participant->setRoles($roles);
    }
}