<?php

namespace App\Service;

use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Repository\ApprenantRepository;
use App\Repository\UserProfilRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\SerializerInterface;

class UserService
{

    private $serializer;
    private $encoder;
    private $validator;
    private $userProfilRepository;
    private $manager;
    private $refineFormDataSrv;

    public function __construct(SerializerInterface $serializer, UserPasswordEncoderInterface $encoder, ValidatorInterface $validator, UserProfilRepository $userProfilRepository, EntityManagerInterface $manager, RefineFormDataSrv $refineFormDataSrv)
    {
        $this->serializer = $serializer;
        $this->encoder = $encoder;
        $this->validator = $validator;
        $this->userProfilRepository = $userProfilRepository;
        $this->manager = $manager;
        $this->refineFormDataSrv = $refineFormDataSrv;
    }

    public function addUser($request, $entity, $type): Response
    {

        $userTab = $request->request->all();
        
        $profil = $this->userProfilRepository->findOneBy(array("libelle" => $type));

        if(isset($userTab['avatar'])) {
            $tempAvatar = $userTab['avatar'];
            unset($userTab['avatar']);
        }

        $user = $this->serializer->denormalize($userTab, $entity, true);
        $user->setProfil($profil);
        $user->setPassword($this->encoder->encodePassword($user, $userTab['password']));

        /**Traitement de l'avatar de l'utilisateur */
        $avatar = (isset($tempAvatar)) ? $tempAvatar : $request->files;
        if (!isset($tempAvatar) && !is_null($avatar->get('avatar'))) {
            $avatarType = explode("/", $avatar->get('avatar')->getMimeType())[1];
            $avatarPath = $avatar->get('avatar')->getRealPath();
            $image = file_get_contents($avatarPath, 'img/img.' . $avatarType);

            $user->setAvatar($image);
        } elseif(isset($tempAvatar)) {
            $user->setAvatar(file_get_contents($avatar));
        }

        /**Validation */
        $errors = $this->validator->validate($user);
        if (($errors) > 0) {
            $errorsString = $this->serializer->serialize($errors, 'json');
            return new JsonResponse($errorsString, Response::HTTP_BAD_REQUEST, [], true);
        }
        
        $this->manager->persist($user);
        $this->manager->flush();

        return new JsonResponse($this->serializer->serialize($user->getId(), 'json'), Response::HTTP_CREATED, [], true);
    }

    public function editUser($request, $repository) {
        $userId = $request->attributes->get('id');
        $user = $repository->find($userId);

        $data = $this->refineFormDataSrv->Refine($request);


       foreach ($data as $key => $value) {
            $setter = 'set'.ucfirst($key);
            if ($key == "password" && $value !== '') {
                $user->$setter($this->encoder->encodePassword($user, $value));
            } elseif($value !== '') {
                $user->$setter($value);
            } 
        }

        $this->manager->flush();
        return new JsonResponse($this->serializer->serialize($user->getId(), 'json'), Response::HTTP_OK, [], true);
    }
}
