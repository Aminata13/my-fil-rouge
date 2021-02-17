<?php

namespace App\Controller;

use App\Entity\Referentiel;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ReferentielRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Entity\CritereAdmission;
use App\Entity\CritereEvaluation;
use App\Repository\CompetenceRepository;
use App\Repository\GroupeCompetenceRepository;
use App\Service\RefineFormDataSrv;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/api")
 */
class ReferentielController extends AbstractController
{
    private $serializer;
    private $validator;
    private $manager;
    private $referentielRepository;
    private $competenceRepository;
    private $groupeCompetenceRepository;
    private $refineFormDataSrv;

    public function __construct(SerializerInterface $serializer, ValidatorInterface $validator, 
    EntityManagerInterface $manager, ReferentielRepository $referentielRepository, 
    GroupeCompetenceRepository $groupeCompetenceRepository, CompetenceRepository $competenceRepository, 
    RefineFormDataSrv $refineFormDataSrv)
    {
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->manager = $manager;
        $this->competenceRepository = $competenceRepository;
        $this->referentielRepository = $referentielRepository;
        $this->groupeCompetenceRepository = $groupeCompetenceRepository;
        $this->refineFormDataSrv = $refineFormDataSrv;
    }

    /**
     * @Security("is_granted('ROLE_ADMIN')")
     * @Route("/admin/referentiels", name="add_referentiel", methods="POST")
     */
    public function addReferentiel(Request $request)
    {
        $data = $request->request->all();
        $referentiel = $this->serializer->denormalize($data, Referentiel::class, true);
        
        $tabLibelle = [];
        foreach ($data['critereAdmissions'] as $value) {
            if ($value != "" && !in_array($value, $tabLibelle)) {
                $tabLibelle[] = $value;
                $critereAdmission = new CritereAdmission();
                $critereAdmission->setLibelle($value);
                $referentiel->addCritereAdmission($critereAdmission);
            }
        }

        $tabLibelle = [];
        foreach ($data['critereEvaluations'] as $value) {
            if ($value != "" && !in_array($value, $tabLibelle)) {
                $tabLibelle[] = $value;
                $critereEvaluation = new CritereEvaluation();
                $critereEvaluation->setLibelle($value);
                $referentiel->addCritereEvaluation($critereEvaluation);
            }
        }
    

        $errors = $this->validator->validate($referentiel);
        if (($errors) > 0) {
            $errorsString = $this->serializer->serialize($errors, 'json');
            return new JsonResponse($errorsString, Response::HTTP_BAD_REQUEST, [], true);
        }

        $file = $request->files->get('programme');
        
        if (is_null($file)) {
            return new JsonResponse("Le programme est requis.", Response::HTTP_BAD_REQUEST, [], true);
        }

        $programme = fopen($file->getRealPath(), "rb");
        $referentiel->setProgramme($programme);
        
        
        $this->manager->persist($referentiel);
        $this->manager->flush();
        return new JsonResponse($this->serializer->serialize($referentiel, 'json'), Response::HTTP_CREATED, [], true);
    }

    /**
     * @Security("is_granted('ROLE_CM')")
     * @Route("/admin/referentiels/{idReferentiel}/groupe_competences/{idGroupeComp}", name="show_competences_by_referentiel", methods="GET")
     */
    public function getCompetenceByReferentiel(int $idReferentiel, int $idGroupeComp): Response
    {
        $data = $this->referentielRepository->findByExampleField($idReferentiel, $idGroupeComp);
        return new JsonResponse($this->serializer->serialize($data, 'json'), Response::HTTP_CREATED, [], true);
    }

    /**
     * @Route("/admin/referentiels/{id}", name="edit_referentiel", methods="PUT")
     */
    public function editReferentiel(Request $request): Response
    {
        $data = $this->refineFormDataSrv->Refine($request);
        dd($data);
    }
}
