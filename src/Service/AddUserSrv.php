<?php

namespace App\Service;

use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Entity\Admin;
use App\Entity\Apprenant;
use App\Entity\Cm;
use App\Entity\Formateur;
use App\Entity\User;
use App\Repository\UserProfilRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\SerializerInterface;

class AddUserSrv
{

    private $serializer;
    private $encoder;
    private $validator;
    private $userProfilRepository;
    private $manager;

    public function __construct(SerializerInterface $serializer, UserPasswordEncoderInterface $encoder, ValidatorInterface $validator, UserProfilRepository $userProfilRepository, EntityManagerInterface $manager)
    {
        $this->serializer = $serializer;
        $this->encoder = $encoder;
        $this->validator = $validator;
        $this->userProfilRepository = $userProfilRepository;
        $this->manager = $manager;
    }

    public function save($request, $entity): Response
    {

        $userTab = $request->request->all();
        
        $profil = $this->findProfil($entity);

        $user = $this->serializer->denormalize($userTab, $entity, true);
        $user->setProfil($profil);
        $user->setPassword($this->encoder->encodePassword($user, $userTab['password']));

        /**Traitement de l'avatar de l'utilisateur */
        $avatar = $request->files;
        if (!is_null($avatar->get('avatar'))) {
            $avatarType = explode("/", $avatar->get('avatar')->getMimeType())[1];
            $avatarPath = $avatar->get('avatar')->getRealPath();
            $image = file_get_contents($avatarPath, 'img/img.' . $avatarType);

            $user->setAvatar($image);
        }

        /**Validation */
        $errors = $this->validator->validate($user);
        if (($errors) > 0) {
            $errorsString = $this->serializer->serialize($errors, 'json');
            return new JsonResponse($errorsString, Response::HTTP_BAD_REQUEST, [], true);
        }
        
        $this->manager->persist($user);
        $this->manager->flush();

        return new JsonResponse('success', Response::HTTP_CREATED, [], true);
    }

    
    public function findProfil($entity) {
        switch ($entity) {
            case ($entity == "App\Entity\Admin"):
                return $this->userProfilRepository->findOneBy(array("libelle" => 'ADMIN'));
                break;
            case ($entity == "App\Entity\Apprenant"):
                return $this->userProfilRepository->findOneBy(array("libelle" => 'APPRENANT'));
                break;
            case ($entity == "App\Entity\Formateur"):
                return $this->userProfilRepository->findOneBy(array("libelle" => 'FORMATEUR'));
                break;
            case ($entity == "App\Entity\Cm"):
                return $this->userProfilRepository->findOneBy(array("libelle" => 'CM'));
                break;
            default:
            return new JsonResponse('Simple utilisateur', Response::HTTP_BAD_REQUEST, [], true);;
                break;
        }
    }
}
