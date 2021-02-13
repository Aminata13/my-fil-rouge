<?php

namespace App\Controller;

use App\Entity\Apprenant;
use App\Repository\ApprenantRepository;
use App\Service\UserService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/api")
 */
class ApprenantController extends AbstractController
{
    private $request;
    private $userService;

    public function __construct(RequestStack $requestStack, UserService $userService)
    {
        $this->request = $requestStack->getCurrentRequest();
        $this->userService = $userService;
    }
    
    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route(path="/apprenants", name="add_apprenant", methods="POST")
     */
    public function addUser(): Response
    {

        return $this->userService->addUser($this->request, Apprenant::class, "APPRENANT");
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route(path="/apprenants/{id}", name="edit_apprenant", methods="PUT")
     */
    public function editUser(Request $request, ApprenantRepository $repository): Response
    {
        return $this->userService->editUser($this->request, $repository);
    }
}
