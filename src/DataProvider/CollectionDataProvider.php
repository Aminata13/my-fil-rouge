<?php

namespace App\DataProvider;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\PaginationExtension;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGenerator;
use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Paginator;
use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use Doctrine\Persistence\ManagerRegistry;

class CollectionDataProvider implements ContextAwareCollectionDataProviderInterface, RestrictedDataProviderInterface
{

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
        $page = (isset($context['filters']['page'])) ? $context['filters']['page'] : 1;
        $size = 10;
        $firstResult = ($page - 1) * $size;

        $queryBuilder = $this->manager
            ->getManagerForClass($resourceClass)
            ->getRepository($resourceClass)
            ->createQueryBuilder('e')
            ->andWhere('e.deleted = :deleted')
            ->setParameter('deleted', false);

        $query = $queryBuilder->getQuery()
            ->setFirstResult($firstResult)
            ->setMaxResults($size);

        $doctrinePaginator = new DoctrinePaginator($query);
        $paginator = new Paginator($doctrinePaginator);

        return $paginator;
    }
}
