<?php

namespace App\DataProvider;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\PaginationExtension;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGenerator;
use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Paginator;
use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;

class UserDataProvider implements ContextAwareCollectionDataProviderInterface, RestrictedDataProviderInterface {
    
    private $manager;
    
    public function __construct(ManagerRegistry $manager)
    {
        $this->manager = $manager;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return $resourceClass === 'App\Entity\User';
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = [])
    {
        $page = (isset($context['filters']['page'])) ? $context['filters']['page'] : 1;
        $size = (isset($context['filters']['size'])) ? $context['filters']['size'] : 3;
        $firstname = (isset($context['filters']['firstname'])) ? $context['filters']['firstname'] : '';

        $firstResult = ($page -1) * $size;

        $queryBuilder = $this->manager
        ->getManagerForClass($resourceClass)
        ->getRepository($resourceClass)
        ->createQueryBuilder('u')
        ->innerJoin('u.profil', 'p')
            ->andWhere('u.deleted = :deleted AND u.firstname LIKE :firstname AND p.libelle != :profil')
            ->setParameters(array('deleted'=>false, 'firstname'=>'%'.$firstname.'%', 'profil'=>'APPRENANT'))
        ;
        
        $query = $queryBuilder->getQuery()
            ->setFirstResult($firstResult)
            ->setMaxResults($size);

        $doctrinePaginator = new DoctrinePaginator($query);
        $paginator = new Paginator($doctrinePaginator);

        return $paginator;
    }
}