<?php

namespace App\Controller;

use App\Entity\Apprenant;
use App\Service\AddUserSrv;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/api")
 */
class ApprenantController extends AbstractController
{
    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route(path="/apprenants", name="add_apprenant", methods="POST")
     */
    public function addUser(Request $request, AddUserSrv $addUserSrv): Response
    {

        return $addUserSrv->save($request, Apprenant::class);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route(path="/apprenants/{id}", name="edit_apprenant", methods="PUT")
     */
    public function editUser(Request $request)
    {
        dump($request->getContent());
        dd('I\'ll take care of it later' );
    }
}
