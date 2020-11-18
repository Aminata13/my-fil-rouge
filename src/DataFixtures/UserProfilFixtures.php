<?php

namespace App\DataFixtures;

use App\Entity\UserProfil;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class UserProfilFixtures extends Fixture
{   
    public const PROFIL_REFERENCE = 'user';
    
    public function load(ObjectManager $manager)
    {
        $profils = array("ADMIN", "FORMATEUR", "APPRENANT", "CM");
        foreach ($profils as $key => $value) {
            $profil = new UserProfil();
            $profil->setLibelle($value);
            
            $manager->persist($profil);
            
            $this->addReference(self::PROFIL_REFERENCE.$key, $profil);
        }
        
        $manager->flush();
        
    }
}
