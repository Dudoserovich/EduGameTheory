<?php

namespace App\Controller;

use App\Entity\Literature;
use App\Entity\Topic;
use App\Entity\TopicLiterature;
use App\Previewer\LiteraturePreviewer;
use App\Previewer\TopicPreviewer;
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

/**
 * @OA\Tag(name="Topic")
 * @Security(name="Bearer")
 */
#[Route('/topics', name: 'topics_')]
class TopicController extends ApiController
{
    private TopicRepository $topicRepository;
    private EntityManagerInterface $em;

    public function __construct(
        TopicRepository $topicRepository,
        EntityManagerInterface $em
    )
    {
        $this->topicRepository = $topicRepository;
        $this->em = $em;
    }

    /**
     * Get all topics ordered
     * @OA\Response(
     *     response=200,
     *     description="HTTP_OK",
     *     @OA\JsonContent(
     *         type="array",
     *         @OA\Items(ref="#/components/schemas/TopicView")
     *     )
     * )
     * @OA\Response(
     *     response=403,
     *     description="Permission deinied"
     * )
     */
    #[Route(name: 'get', methods: ['GET'])]
    public function getTopics(TopicPreviewer $topicPreviewer): JsonResponse
    {
        $literaturePreviewers = array_map(
            fn(Topic $topic): array => $topicPreviewer->preview($topic),
            $this->topicRepository->findBy([], ["name" => "ASC"])
        );

        return $this->response($literaturePreviewers);
    }

    /**
     * Add new topic
     * @OA\RequestBody (
     *     required=true,
     *     @OA\JsonContent(
     *         @OA\Property(property="name", ref="#/components/schemas/TopicView/properties/name")
     *     )
     * )
     * @OA\Response(
     *     response=200,
     *     description="Topic added successfully"
     * )
     * * @OA\Response(
     *     response=403,
     *     description="Permission denied!"
     * )
     * * @OA\Response(
     *     response=422,
     *     description="Data no valid"
     * )
     */
    #[Route(name: 'post', methods: ['POST'])]
    public function postTopic(
        Request $request,
        TopicRepository $topicRepository
    ): JsonResponse
    {
        $request = $request->request->all();

//        $this->setSoftDeleteable(false);
        $topic = $this->topicRepository->findOneBy(['name' => $request['name']]);
        if ($topic)
            return $this->respondValidationError('A topic with such name has already been created');

        $topic = new Topic();
        try {
            $topic
                ->setName($request['name']);
            $this->em->persist($topic);

            $this->em->flush();
            return $this->respondWithSuccess("Topic added successfully");
        } catch (Exception) {
            return $this->respondValidationError();
        }
    }

    /**
     * Topic object
     * @OA\Response(
     *     response=200,
     *     description="HTTP_OK",
     *     @OA\JsonContent(ref="#/components/schemas/TopicView")
     * )
     * @OA\Response(
     *     response=403,
     *     description="Permission denied!"
     * )
     * @OA\Response(
     *     response=404,
     *     description="Topic not found"
     * )
     */
    #[Route('/{topicId}',
        name: 'get_by_id',
        requirements: ['topicId' => '\d+'],
        methods: ['GET'])
    ]
    public function getTopic(
        TopicPreviewer $topicPreviewer,
        int $topicId): JsonResponse
    {
        $topic = $this->topicRepository->find($topicId);
        if (!$topic) {
            return $this->respondNotFound("Topic not found");
        }

        return $this->response($topicPreviewer->preview($topic));
    }

    /**
     * Change field of topic
     * @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *         @OA\Property(property="name", nullable=false, ref="#/components/schemas/TopicView/properties/name")
     *     )
     * )
     * @OA\Response(
     *     response=200,
     *     description="Topic updated successfully"
     * )
     *
     * @OA\Response(
     *     response=403,
     *     description="Permission denied!"
     * )
     * @OA\Response(
     *     response=404,
     *     description="Topic not found"
     * )
     * @OA\Response(
     *     response=422,
     *     description="Data no valid"
     * )
     */
    #[Route(
        '/{topicId}',
        name: 'put_by_id',
        requirements: ['topicId' => '\d+'],
        methods: ['PUT']
    )]
    public function upTopic(Request $request,
                                 int $topicId,
                                 TopicLiteratureRepository $topicLiteratureRepository,
                                 TopicRepository $topicRepository): JsonResponse
    {
        $topic = $this->topicRepository->find($topicId);
        if (!$topic) {
            return $this->respondNotFound("Topic not found");
        }

        $request = $request->request->all();

        try {
            if (isset($request['name'])) {
                $topic->setName($request['name']);
            }

            $this->em->flush();

            return $this->respondWithSuccess("Topic updated successfully");
        } catch (Exception) {
            return $this->respondValidationError();
        }
    }

    /**
     * Delete topic
     * @OA\Response(
     *     response=200,
     *     description="Topic deleted successfully"
     * )
     * @OA\Response(
     *     response=403,
     *     description="Permission denied!"
     * )
     * @OA\Response(
     *     response=404,
     *     description="Topic not found"
     * )
     */
    #[Route(
        '/{topicId}',
        name: 'delete_by_id',
        requirements: ['topicId' => '\d+'],
        methods: ['DELETE']
    )]
    public function delTopic(int $topicId): JsonResponse
    {
        $literature = $this->topicRepository->find($topicId);
        if (!$literature) {
            return $this->respondNotFound("Topic not found");
        }

        $this->em->remove($literature);
        $this->em->flush();

        return $this->respondWithSuccess("Topic deleted successfully");
    }
}
