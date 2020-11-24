<?php

namespace App\Controller;

use App\Entity\Cm;
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
class CmController extends AbstractController
{
    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route(path="/cms", name="add_cm", methods="POST")
     */
    public function addUser(Request $request, AddUserSrv $addUserSrv, EntityManagerInterface $em)
    {
        return $addUserSrv->save($request, Cm::class);
    }
}
