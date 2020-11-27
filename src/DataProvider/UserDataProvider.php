<?php

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Repository\UserProfilRepository;
use App\Repository\UserRepository;

class UserDataProvider implements ContextAwareCollectionDataProviderInterface, RestrictedDataProviderInterface {
    
    private $userRepository;
    private $userProfilRepository;
    
    public function __construct(UserRepository $userRepository, UserProfilRepository $userProfilRepository)
    {
        $this->userRepository = $userRepository;
        $this->userProfilRepository = $userRepository;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return $resourceClass === User::class;
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = [])
    {
        return $this->userProfilRepository->findAll();
    }
}