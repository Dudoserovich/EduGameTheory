<?php

namespace App\Controller;

use App\Entity\Literature;
use App\Entity\TopicLiterature;
use App\Previewer\LiteraturePreviewer;
use App\Repository\LiteratureRepository;
use App\Repository\TopicLiteratureRepository;
use App\Repository\TopicRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;

#[Route('/literatures', name: 'literatures_')]
class LiteratureController extends ApiController
{
    private LiteratureRepository $literatureRepository;
    private EntityManagerInterface $em;

    public function __construct(LiteratureRepository $literatureRepository, EntityManagerInterface $em)
    {
        $this->literatureRepository = $literatureRepository;
        $this->em = $em;
    }

    # TODO: Сто процентов нужна пагинация
    #   + нормальный поиск с учётом фильтров (топиков литературы)
    /**
     * Get all literatures ordered
     * @OA\Response(
     *     response=200,
     *     description="HTTP_OK",
     *     @OA\JsonContent(
     *         type="array",
     *         @OA\Items(ref="#/components/schemas/LiteratureView")
     *     )
     * )
     * @OA\Response(
     *     response=403,
     *     description="Permission deinied"
     * )
     * @OA\Tag(name="Literature")
     * @Security(name="Bearer")
     */
    #[Route(name: 'get', methods: ['GET'])]
    public function getLiteratures(LiteraturePreviewer $literaturePreviewer): JsonResponse
    {
        $literaturePreviewers = array_map(
            fn(Literature $literature): array => $literaturePreviewer->preview($literature),
            $this->literatureRepository->findBy([], ["name" => "ASC"])
        );

        return $this->response($literaturePreviewers);
    }

    /**
     * Add new literature
     * @OA\RequestBody (
     *     required=true,
     *     @OA\JsonContent(
     *         @OA\Property(property="name", ref="#/components/schemas/LiteratureView/properties/name"),
     *         @OA\Property(property="link", ref="#/components/schemas/LiteratureView/properties/link"),
     *         @OA\Property(property="topic_id", ref="#/components/schemas/Topic/properties/id")
     *     )
     * )
     * @OA\Response(
     *     response=200,
     *     description="Literature added successfully"
     * )
     * * @OA\Response(
     *     response=403,
     *     description="Permission denied!"
     * )
     * * @OA\Response(
     *     response=422,
     *     description="Data no valid"
     * )
     * @OA\Tag(name="Literature")
     * @Security(name="Bearer")
     */
    #[Route(name: 'post', methods: ['POST'])]
    public function postLiterature(Request $request, TopicRepository $topicRepository): JsonResponse
    {
        $request = $request->request->all();

//        $this->setSoftDeleteable(false);
        $literature = $this->literatureRepository->findOneBy(['name' => $request['name']]);
        if ($literature)
            return $this->respondValidationError('A literature with such name has already been created');

        $topic = $topicRepository->find($request['topic_id']);
        if (!$topic) {
            return $this->respondNotFound("Topic not found");
        }

        $literature = new Literature();
        try {
            $literature
                ->setName($request['name'])
                ->setLink($request['link']);
            $this->em->persist($literature);

            $topicLiterature = new TopicLiterature();
            $topicLiterature
                ->setLiterature($literature)
                ->setTopic($topic);
            $this->em->persist($topicLiterature);

            $this->em->flush();
            return $this->respondWithSuccess("Literature added successfully");
        } catch (Exception) {
            return $this->respondValidationError();
        }
    }

    /**
     * Literature object
     * @OA\Response(
     *     response=200,
     *     description="HTTP_OK",
     *     @OA\JsonContent(ref="#/components/schemas/LiteratureView")
     * )
     * @OA\Response(
     *     response=403,
     *     description="Permission denied!"
     * )
     * @OA\Response(
     *     response=404,
     *     description="Literature not found"
     * )
     * @OA\Tag(name="Literature")
     * @Security(name="Bearer")
     */
    #[Route('/{literatureId}',
        name: 'get_by_id',
        requirements: ['literatureId' => '\d+'],
        methods: ['GET'])
    ]
    public function getLiterature(
        LiteraturePreviewer $literaturePreviewer,
        int $literatureId): JsonResponse
    {
        $literature = $this->literatureRepository->find($literatureId);
        if (!$literature) {
            return $this->respondNotFound("Literature not found");
        }

        return $this->response($literaturePreviewer->preview($literature));
    }

    /**
     * Change field of literature
     * @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *         @OA\Property(property="name", nullable=false, ref="#/components/schemas/LiteratureView/properties/name"),
     *         @OA\Property(property="link", nullable=true, ref="#/components/schemas/LiteratureView/properties/link"),
     *         @OA\Property(property="old_topic_id", ref="#/components/schemas/Topic/properties/id"),
     *         @OA\Property(property="topic_id", ref="#/components/schemas/Topic/properties/id")
     *     )
     * )
     * @OA\Response(
     *     response=200,
     *     description="Literature updated successfully"
     * )
     * @OA\Response(
     *     response=403,
     *     description="Permission denied!"
     * )
     * @OA\Response(
     *     response=404,
     *     description="Literature not found"
     * )
     * @OA\Response(
     *     response=422,
     *     description="Data no valid"
     * )
     * @OA\Tag(name="Literature")
     * @Security(name="Bearer")
     */
    #[Route('/{literatureId}', name: 'put_by_id', requirements: ['literatureId' => '\d+'], methods: ['PUT'])]
    public function upLiterature(Request $request,
                                 int $literatureId,
                                 TopicLiteratureRepository $topicLiteratureRepository,
                                 TopicRepository $topicRepository): JsonResponse
    {
        $literature = $this->literatureRepository->find($literatureId);
        if (!$literature) {
            return $this->respondNotFound("Literature not found");
        }

        $request = $request->request->all();

        try {
            if (isset($request['name'])) {
                $literature->setName($request['name']);
            }
            if (isset($request['link'])) {
                $literature->setLink($request['link']);
            }
            if (isset($request['topic_id'])) {
                $newTopic = $topicRepository->find($request['topic_id']);
                $topicLiterature = $topicLiteratureRepository->findOneBy(["literature" => $literature, "topic" => $newTopic]);

                // Попытка заменить топик на уже существующий
                if ($topicLiterature)
                    return $this->respondWithSuccess("Literature is already exists");
                else { // Если меняем топик, нам нужно заменить на новый
                    $oldTopic = $topicRepository->find($request["old_topic_id"]);
                    $topicLiterature = $topicLiteratureRepository->findOneBy(["literature" => $literature, "topic" => $oldTopic]);

                    $topicLiterature->setTopic($newTopic);
                }
            }

            $this->em->flush();

            return $this->respondWithSuccess("Literature updated successfully");
        } catch (Exception) {
            return $this->respondValidationError();
        }
    }

    /**
     * Delete literature
     * @OA\Response(
     *     response=200,
     *     description="Literature deleted successfully"
     * )
     * @OA\Response(
     *     response=403,
     *     description="Permission denied!"
     * )
     * @OA\Response(
     *     response=404,
     *     description="Literature not found"
     * )
     * @OA\Tag(name="Literature")
     * @Security(name="Bearer")
     */
    #[Route('/{literatureId}', name: 'delete_by_id', requirements: ['literatureId' => '\d+'], methods: ['DELETE'])]
    public function delLiterature(int $literatureId): JsonResponse
    {
        $literature = $this->literatureRepository->find($literatureId);
        if (!$literature) {
            return $this->respondNotFound("Literature not found");
        }

        $this->em->remove($literature);
        $this->em->flush();

        return $this->respondWithSuccess("Literature deleted successfully");
    }
}
