<?php

namespace App\Controller;

use App\Entity\Admin;
use App\Repository\AdminRepository;
use App\Service\UserService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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
}
