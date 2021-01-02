<?php

namespace App\Controller;

use App\Repository\CompetenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Entity\GroupeCompetence;
use App\Repository\GroupeCompetenceRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/api")
 */
class GroupeCompetenceController extends AbstractController
{
    private $serializer;
    private $validator;
    private $manager;
    private $competenceRepository;
    private $groupeCompetenceRepository;


    public function __construct(SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $manager, CompetenceRepository $competenceRepository, GroupeCompetenceRepository $groupeCompetenceRepository)
    {
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->manager = $manager;
        $this->competenceRepository = $competenceRepository;
        $this->groupeCompetenceRepository = $groupeCompetenceRepository;
    }

    /**
     * @Security("is_granted('ROLE_ADMIN')")
     * @Route("/admin/groupe_competences", name="add_groupe_competence", methods="POST")
     */
    public function addGroupeCompetence(Request $request): Response
    {
        $data = $this->serializer->denormalize(json_decode($request->getContent(), true), GroupeCompetence::class, true);

        $errors = $this->validator->validate($data);
        if (($errors) > 0) {
            $errorsString = $this->serializer->serialize($errors, 'json');
            return new JsonResponse($errorsString, Response::HTTP_BAD_REQUEST, [], true);
        }

        $competences = $data->getCompetences();

        $groupeCompetence = new GroupeCompetence();
        $groupeCompetence->setLibelle($data->getLibelle());
        $groupeCompetence->setDescription($data->getDescription());

        foreach ($competences as $value) {
            if (!empty($value->getLibelle())) {
                $competence = $this->competenceRepository->findOneBy(array('libelle' => $value->getLibelle()));
                if (!is_null($competence)) {
                    $groupeCompetence->addCompetence($competence);
                }
            }
        }

        if (count($groupeCompetence->getCompetences()) < 1) {
            return new JsonResponse("Une compétence est requise.", Response::HTTP_BAD_REQUEST, [], true);
        }

        $this->manager->persist($groupeCompetence);
        $this->manager->flush();

        return new JsonResponse($this->serializer->serialize($groupeCompetence, 'json'), Response::HTTP_CREATED, [], true);
    }

    /**
     * @Security("is_granted('ROLE_CM')")
     * @Route("/admin/groupe_competences/{id}", name="edit_groupe_competence", methods="PUT")
     */
    public function editGroupeCompetence(int $id, Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $groupeCompetence = $this->groupeCompetenceRepository->find($id);

        if (is_null($groupeCompetence)) {
            return new JsonResponse("Ce groupe de compétences n'existe pas.", Response::HTTP_BAD_REQUEST, [], true);
        }

        foreach ($data as $key => $value) {
            $setter = 'set' . ucfirst($key);
            if ($key !== "competences" && $value !== '') {
                $groupeCompetence->$setter($value);
            }
        }

        $competences = $data['competences'];
        foreach ($competences as $value) {
            $libelle = $value['libelle'];
            $action = $value['action'];

            if (!empty($libelle)) {
                $competence  = $this->competenceRepository->findOneBy(array("libelle" => $libelle));

                if ($action == 'assignment') {
                    $groupeCompetence->addCompetence($competence);
                } elseif ($action == 'deletion') {
                    $groupeCompetence->removeCompetence($competence);
                }
            }
        }

        $this->manager->flush();
        return new JsonResponse($this->serializer->serialize($groupeCompetence, 'json'), Response::HTTP_OK, [], true);
    }
}
