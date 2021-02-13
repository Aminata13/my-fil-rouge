<?php

namespace App\Controller;

use App\Entity\Admin;
use App\Repository\AdminRepository;
use App\Repository\UserRepository;
use App\Service\UserService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/api")
 */
class AdminController extends AbstractController
{
    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route(path="/admins", name="add_admin", methods={"POST"})
     */
    public function addUser(Request $request, UserService $userService): Response
    {

        return $userService->addUser($request, Admin::class, "ADMIN");
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route(path="/admins/{id}", name="edit_admin", methods="PUT")
     */
    public function editUser(Request $request, AdminRepository $repository, UserService $userService): Response
    {
        return $userService->editUser($request, $repository);
    }

    /**
     * @Route(path="/admin/users/{username}/check", name="find_user_by_username", methods="GET")
     */
    public function getUserByUsername(SerializerInterface $serializer, string $username, UserRepository $userRepository): Response
    {
        $user = $userRepository->findOneBy(array('username' => $username));
        if ($user) {
            return new JsonResponse($serializer->serialize($user, 'json'), Response::HTTP_OK, [], true);
        }
        return new JsonResponse(null, Response::HTTP_BAD_REQUEST, [], true);
    }
}
