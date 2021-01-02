<?php

namespace App\DataPersister;

use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Entity\Competence;

class CompetenceDataPersister implements DataPersisterInterface
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    public function supports($data): bool
    {
        // TODO: Implement supports() method.
        return $data instanceof Competence;
    }

    /**
     * @param Competence $data
     */
    public function persist($data)
    {
        // TODO: Implement persist() method.
        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }

    /**
     * @param Competence $data
     */
    public function remove($data)
    {
        // TODO: Implement remove() method.
        $data->setDeleted(true);
        $groupeCompetences = $data->getGroupeCompetences();
        foreach ($groupeCompetences as $value) {
            $data->removeGroupeCompetence($value);
        }

        $this->entityManager->flush();
    }
}