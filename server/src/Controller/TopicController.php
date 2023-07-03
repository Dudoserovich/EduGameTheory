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
     * Получение всех типов, отсортированных по названию
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
     *     description="Доступ запрещён"
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
     *     description="Тип добавлен успешно"
     * )
     * * @OA\Response(
     *     response=403,
     *     description="Доступ запрещён"
     * )
     * * @OA\Response(
     *     response=422,
     *     description="Неверные данные"
     * )
     */
    #[Route(name: 'post', methods: ['POST'])]
    public function postTopic(
        Request $request,
        TopicRepository $topicRepository
    ): JsonResponse
    {
        $request = $request->request->all();

        $this->setSoftDeleteable(false);
        $topic = $this->topicRepository->findOneBy(['name' => $request['name']]);
        if ($topic)
            if (!$topic->getDeletedAt())
                return $this->respondValidationError('Тип с таким названием уже создан');
            else $topic->setDeletedAt(null);

        $topic = $topic ?? new Topic();
        try {
            $topic
                ->setName($request['name']);
            $this->em->persist($topic);

            $this->em->flush();
            return $this->respondWithSuccess("Тип добавлен успешно");
        } catch (Exception) {
            return $this->respondValidationError();
        }
    }

    /**
     * Объект типа
     * @OA\Response(
     *     response=200,
     *     description="HTTP_OK",
     *     @OA\JsonContent(ref="#/components/schemas/TopicView")
     * )
     * @OA\Response(
     *     response=403,
     *     description="Доступ запрещён"
     * )
     * @OA\Response(
     *     response=404,
     *     description="Тип не найден"
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
            return $this->respondNotFound("Тип не найден");
        }

        return $this->response($topicPreviewer->preview($topic));
    }

    /**
     * Изменение полей Типа
     * @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *         @OA\Property(property="name", nullable=false, ref="#/components/schemas/TopicView/properties/name")
     *     )
     * )
     * @OA\Response(
     *     response=200,
     *     description="Тип обновлён успешно"
     * )
     *
     * @OA\Response(
     *     response=403,
     *     description="Доступ запрещён"
     * )
     * @OA\Response(
     *     response=404,
     *     description="Тип не найден"
     * )
     * @OA\Response(
     *     response=422,
     *     description="Неверные данные"
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
            return $this->respondNotFound("Тип не найден");
        }

        $request = $request->request->all();

        try {
            if (isset($request['name'])) {
                $topic->setName($request['name']);
            }

            $this->em->flush();

            return $this->respondWithSuccess("Тип обновлён успешно");
        } catch (Exception) {
            return $this->respondValidationError();
        }
    }

    /**
     * Удаление Типа
     * @OA\Response(
     *     response=200,
     *     description="Тип удалён успешно"
     * )
     * @OA\Response(
     *     response=403,
     *     description="Доступ запрещён"
     * )
     * @OA\Response(
     *     response=404,
     *     description="Тип не найден"
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
            return $this->respondNotFound("Тип не найден");
        }

        $this->em->remove($literature);
        $this->em->flush();

        return $this->respondWithSuccess("Тип удалён успешно");
    }
}
