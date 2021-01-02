<?php

namespace App\DataPersister;

use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Entity\GroupeCompetence;

class GroupeCompetenceDataPersister implements DataPersisterInterface
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    public function supports($data): bool
    {
        // TODO: Implement supports() method.
        return $data instanceof GroupeCompetence;
    }

    /**
     * @param GroupeCompetence $data
     */
    public function persist($data)
    {
        // TODO: Implement persist() method.
        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }

    /**
     * @param GroupeCompetence $data
     */
    public function remove($data)
    {
        // TODO: Implement remove() method.
        $data->setDeleted(true);
        $competences = $data->getCompetences();
        foreach ($competences as $value) {
            $data->removeCompetence($value);
        }

        $this->entityManager->flush();
    }
}