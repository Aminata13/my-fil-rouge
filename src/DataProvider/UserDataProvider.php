<?php

namespace App\DataProvider;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\PaginationExtension;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGenerator;
use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;

class UserDataProvider implements ContextAwareCollectionDataProviderInterface, RestrictedDataProviderInterface {
    
    private $userRepository;
    private $paginator;
    private $manager;
    
    public function __construct(UserRepository $userRepository, PaginationExtension $paginator, ManagerRegistry $manager)
    {
        $this->userRepository = $userRepository;
        $this->paginator = $paginator;
        $this->manager = $manager;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return $resourceClass === 'App\Entity\User';
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = [])
    {
        $queryBuilder = $this->manager
        ->getManagerForClass($resourceClass)
        ->getRepository($resourceClass)
        ->createQueryBuilder('u')
        ->innerJoin('u.profil', 'p')
            ->andWhere('u.deleted = :deleted AND p.libelle != :profil')
            ->setParameters(array('deleted'=>false, 'profil'=>'APPRENANT'))
        ;
        
        $this->paginator->applyToCollection($queryBuilder, new QueryNameGenerator, $resourceClass, $operationName, $context);

        return $queryBuilder->getQuery()->getResult();
    }
}