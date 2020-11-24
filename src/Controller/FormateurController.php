<?php

namespace App\Controller;

use App\Entity\Formateur;
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
class FormateurController extends AbstractController
{
    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route(path="/formateurs", name="add_formateur", methods={"POST"})
     */
    public function addUser(Request $request, AddUserSrv $addUserSrv, EntityManagerInterface $em)
    {

        return $addUserSrv->save($request, Formateur::class);
    }
}
