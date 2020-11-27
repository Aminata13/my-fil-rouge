<?php

namespace App\Controller;

use App\Entity\Tag;
use App\Entity\GroupeTag;
use App\Repository\TagRepository;
use App\Repository\GroupeTagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use ApiPlatform\Core\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/api")
 */
class GroupeTagController extends AbstractController
{
    private $serializer;
    private $validator;
    private $manager;
    private $tagRepository;
    private $groupeTagRepository;

    public function __construct(SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $manager, TagRepository $tagRepository, GroupeTagRepository $groupeTagRepository)
    {
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->manager = $manager;
        $this->tagRepository = $tagRepository;
        $this->groupeTagRepository = $groupeTagRepository;
    }

    /**
     * @Security("is_granted('ROLE_ADMIN') and is_granted('ROLE_FORMATEUR')")
     * @Route("/admin/groupe_tags", name="add_groupe_tag", methods="POST")
     */
    public function addGroupeTag(Request $request): Response
    {
        dd('toto');
        $data = $this->serializer->denormalize(json_decode($request->getContent(), true), GroupeTag::class, true);

        $errors = $this->validator->validate($data);
        if (($errors) > 0) {
            $errorsString = $this->serializer->serialize($errors, 'json');
            return new JsonResponse($errorsString, Response::HTTP_BAD_REQUEST, [], true);
        }

        $tags = $data->getTags();

        if (count($tags) < 1) {
            return new JsonResponse("Un tag est requis.", Response::HTTP_BAD_REQUEST, [], true);
        }

        $groupeTag = new GroupeTag();
        $groupeTag->setLibelle($data->getLibelle());

        $tabLibelle = [];
        foreach ($tags as $value) {
            if (!empty($value->getLibelle())) {
                $tag = $this->tagRepository->findOneBy(array('libelle' => $value->getLibelle()));
                if (is_null($tag) && !in_array($value->getlibelle(), $tabLibelle)) {
                    $tabLibelle[] = $value->getlibelle();
                    $tag = new Tag();
                    $tag->setLibelle($value->getLibelle());
                }
                $groupeTag->addTag($tag);
            }
        }

        if (count($groupeTag->getTags()) < 1) {
            return new JsonResponse("Le libelle d'un tag est requis.", Response::HTTP_BAD_REQUEST, [], true);
        }

        $this->manager->persist($groupeTag);
        $this->manager->flush();
        return new JsonResponse("success", Response::HTTP_CREATED, [], true);
    }

    /**
     * @Security("is_granted('ROLE_ADMIN') and is_granted('ROLE_FORMATEUR')")
     * @Route("/admin/groupe_tags/{id}", name="edit_groupe_tag", methods="PUT")
     */
    public function editGroupeTag(int $id, Request $request): Response
    {
        dd('tata');
        $data = json_decode($request->getContent(), true);
        $groupeTag = $this->groupeTagRepository->find($id);

        if (is_null($groupeTag)) {
            return new JsonResponse("Ce groupe de tags n'existe pas.", Response::HTTP_BAD_REQUEST, [], true);
        }

        if (!empty($data['libelle'])) {
            $groupeTag->setLibelle($data['libelle']);
        }

        $tags = $data['tags'];
        foreach ($tags as $value) {
            $libelle = $value['libelle'];
            $action = $value['action'];
            
            if ($action == 'assignment' && !empty($libelle)) 
            {
                $tag  = $this->tagRepository->findOneBy(array("libelle" => $libelle));
                $groupeTag->addTag($tag);
            } elseif ($action == 'deletion' && !empty($libelle)) 
            {
                $tag  = $this->tagRepository->findOneBy(array("libelle" => $libelle));
                $groupeTag->removeTag($tag);
            } elseif(!empty($libelle)) 
            {
                $tag = new Tag();
                $tag->setLibelle($libelle);
                $groupeTag->addTag($tag);
                
                $this->manager->persist($tag);
            }
        }

        $this->manager->flush();
        return new JsonResponse('success', Response::HTTP_OK, [], true);
    }
}
