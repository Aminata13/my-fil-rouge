<?php

namespace App\Controller;

use App\Entity\Cm;
use App\Repository\CmRepository;
use App\Service\UserService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/api")
 */
class CmController extends AbstractController
{
    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route(path="/cms", name="add_cm", methods="POST")
     */
    public function addUser(Request $request, UserService $userService): Response
    {
        return $userService->addUser($request, Cm::class, "CM");
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route(path="/cms/{id}", name="edit_cm", methods="PUT")
     */
    public function editUser(Request $request, CmRepository $repository, UserService $userService): Response
    {
        return $userService->editUser($request, $repository);
    }
}
