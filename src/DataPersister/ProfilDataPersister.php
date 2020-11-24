<?php

namespace App\DataPersister;

use App\Entity\UserProfil;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\Core\DataPersister\DataPersisterInterface;

class ProfilDataPersister implements DataPersisterInterface
{
    private $entityManager;
    private $userRepository;

    public function __construct(EntityManagerInterface $entityManager, UserRepository $userRepository)
    {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
    }
    
    public function supports($data): bool
    {
        // TODO: Implement supports() method.
        return $data instanceof UserProfil;
    }

    /**
     * @param UserProfil $data
     */
    public function persist($data)
    {
        // TODO: Implement persist() method.
        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }

    /**
     * @param UserProfil $data
     */
    public function remove($data)
    {
        // TODO: Implement remove() method.
        $data->setDeleted(true);

        //archiving all the users with the profil
        $users = $this->userRepository->findBy(array("profil"=>$data));
        foreach ($users as $u) {
            $u->setDeleted(true);
        }
        
        $this->entityManager->flush();
    }
}