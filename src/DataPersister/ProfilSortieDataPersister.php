<?php

namespace App\DataPersister;

use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Entity\ProfilSortie;
use App\Repository\ApprenantRepository;

class ProfilSortieDataPersister implements DataPersisterInterface
{
    private $entityManager;
    private $apprenantRepository;

    public function __construct(EntityManagerInterface $entityManager, ApprenantRepository $apprenantRepository)
    {
        $this->entityManager = $entityManager;
        $this->apprenantRepository = $apprenantRepository;
    }
    
    public function supports($data): bool
    {
        // TODO: Implement supports() method.
        return $data instanceof ProfilSortie;
    }

    /**
     * @param ProfilSortie $data
     */
    public function persist($data)
    {
        // TODO: Implement persist() method.
        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }

    /**
     * @param ProfilSortie $data
     */
    public function remove($data)
    {
        // TODO: Implement remove() method.
        $data->setDeleted(true);

        // loading..
        $apprenants = $this->apprenantRepository->findBy(array("profilSortie"=>$data));
        foreach ($apprenants as $a) {
            $a->setProfilSortie(null);
        }
        
        $this->entityManager->flush();
    }
}