<?php

namespace App\Controller;

use App\Entity\Formateur;
use App\Repository\FormateurRepository;
use App\Service\UserService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/api")
 */
class FormateurController extends AbstractController
{
    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route(path="/formateurs", name="add_formateur", methods={"POST"})
     */
    public function addUser(Request $request, UserService $userService): Response
    {

        return $userService->addUser($request, Formateur::class, "FORMATEUR");
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route(path="/formateurs/{id}", name="edit_formateur", methods="PUT")
     */
    public function editUser(Request $request, FormateurRepository $repository, UserService $userService): Response
    {
        return $userService->editUser($request, $repository);
    }
}
