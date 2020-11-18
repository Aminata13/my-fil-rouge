<?php

namespace App\DataFixtures;

use App\Entity\Cm;
use Faker\Factory;
use App\Entity\Admin;
use App\Entity\Apprenant;
use App\Entity\Formateur;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture implements DependentFixtureInterface
{   
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }
    
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        $userTab = array(Admin::class, Formateur::class, Apprenant::class, Cm::class);

        for ($i=0; $i<count($userTab); $i++) { 
            $user = new $userTab[$i];
            $password = $this->encoder->encodePassword($user, 'password');
            
            $user
                ->setUsername($faker->userName)
                ->setPassword($password)
                ->setProfil($this->getReference(UserProfilFixtures::PROFIL_REFERENCE.$i))
                ->setLastname($faker->lastName)
                ->setFirstname($faker->firstName)
                ->setEmail($faker->email)
                ->setAdress($faker->address);
                
    
            $manager->persist($user);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            UserProfilFixtures::class,
        );
    }
}
