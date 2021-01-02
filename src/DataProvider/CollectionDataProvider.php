<?php

namespace App\DataProvider;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\PaginationExtension;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGenerator;
use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use Doctrine\Persistence\ManagerRegistry;

class CollectionDataProvider implements ContextAwareCollectionDataProviderInterface, RestrictedDataProviderInterface {
    
    private $paginator;
    
    public function __construct(PaginationExtension $paginator, ManagerRegistry $manager)
    {
        $this->paginator = $paginator;
        $this->manager = $manager;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return $resourceClass !== 'App\Entity\User' && str_contains($resourceClass, 'App\Entity');
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = [])
    {
        $queryBuilder = $this->manager
        ->getManagerForClass($resourceClass)
        ->getRepository($resourceClass)
        ->createQueryBuilder('e')
        ->andWhere('e.deleted = :deleted')
        ->setParameter('deleted', false)
        ;
        
        $this->paginator->applyToCollection($queryBuilder, new QueryNameGenerator, $resourceClass, $operationName, $context);

        return $queryBuilder->getQuery()->getResult();
    }
}