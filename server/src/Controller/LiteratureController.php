<?php

namespace App\Controller;

use App\Entity\Literature;
use App\Entity\TopicLiterature;
use App\Previewer\LiteraturePreviewer;
use App\Previewer\TopicLiteraturePreviewer;
use App\Repository\LiteratureRepository;
use App\Repository\TopicLiteratureRepository;
use App\Repository\TopicRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Literature")
 * @Security(name="Bearer")
 */
#[Route('/literatures', name: 'literatures_')]
class LiteratureController extends ApiController
{
    private LiteratureRepository $literatureRepository;
    private EntityManagerInterface $em;
    private TopicLiteratureRepository $topicLiteratureRepository;

    public function __construct(
        LiteratureRepository      $literatureRepository,
        TopicLiteratureRepository $topicLiteratureRepository,
        EntityManagerInterface    $em
    )
    {
        $this->literatureRepository = $literatureRepository;
        $this->topicLiteratureRepository = $topicLiteratureRepository;
        $this->em = $em;
    }

    # TODO: Сто процентов нужна пагинация
    #   + нормальный поиск с учётом фильтров (топиков литературы)
    /**
     * Получение списка литературы отсортированного по названию
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
     *     description="Доступ запрещён"
     * )
     */
    #[Route(name: 'get', methods: ['GET'])]
    public function getLiteratures(
        LiteraturePreviewer      $literaturePreviewer,
        TopicLiteraturePreviewer $topicLiteraturePreviewer
    ): JsonResponse
    {
        $literatures = $this->literatureRepository->findBy([], ["name" => "ASC"]);

        $literaturePreviewers = array_map(
            fn(Literature $literature): array => $topicLiteraturePreviewer->preview($literature),
            $literatures
        );

        return $this->response($literaturePreviewers);
    }

    /**
     * Добавление новой литературы
     * @OA\RequestBody(
     *     description="P.S. svg загрузить не получится",
     *     required=true,
     *     @OA\MediaType(
     *         mediaType="multipart/form-data",
     *         @OA\Schema(
     *             @OA\Property(
     *                  property="name",
     *                  ref="#/components/schemas/LiteratureView/properties/name"
     *             ),
     *             @OA\Property(
     *                  property="description",
     *                  ref="#/components/schemas/LiteratureView/properties/description"
     *             ),
     *             @OA\Property(
     *                  property="link",
     *                  ref="#/components/schemas/LiteratureView/properties/link"
     *             ),
     *            @OA\Property(property="topic_id", ref="#/components/schemas/Topic/properties/id"),
     *            @OA\Property(
     *                 property="imageFile",
     *                 nullable=false,
     *                 ref="#/components/schemas/AchievementView/properties/imageFile"
     *            ),
     *         )
     *     )
     * )
     * @OA\Response(
     *     response=200,
     *     description="Литература успешно добавлена"
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
    public function postLiterature(
        Request                   $request,
        TopicRepository           $topicRepository,
        TopicLiteratureRepository $topicLiteratureRepository,
        FileUploader              $fileUploader
    ): JsonResponse
    {
        /**
         * @var $imageFile File
         */
        $imageFile = $request->files->get('imageFile');
        if (!$imageFile) {
            return $this->respondValidationError("Изображение для литературы не передано");
        }

        $request = $request->request->all();

        // Проверяем, что файл - изображение
        if (!$fileUploader->isImage($imageFile))
            return $this->respondValidationError("Неверный тип изображения" . $imageFile->getMimeType());


        $topic = $topicRepository->find($request['topic_id']);
        if (!$topic) {
            return $this->respondNotFound("Тип не передан");
        }

        $this->setSoftDeleteable($this->em, false);
        $literature = $this->literatureRepository->findOneBy(['name' => $request['name']]);

        if ($literature) {
            if ($literature->getDeletedAt()) {
                $literature->setDeletedAt(null);
                $literature
                    ->setImageFile($imageFile)
                    ->setName($request['name'])
                    ->setDescription($request['description'])
                    ->setLink($request['link']);

                $topicLiterature = $topicLiteratureRepository->findOneBy(
                    ["topic" => $topic,
                        "literature" => $literature]
                );
                $topicLiterature = $topicLiterature ?? new TopicLiterature();
                $topicLiterature
                    ->setLiterature($literature)
                    ->setTopic($topic);
                $this->em->persist($topicLiterature);

                $this->em->flush();
                $this->setSoftDeleteable($this->em);
                return $this->respondWithSuccess("Литература успешно добавлена");
            }

            return $this->respondValidationError('Литература с таким названием уже создана');
        }

        $literature = new Literature();
        try {
            $literature
                ->setImageFile($imageFile)
                ->setName($request['name'])
                ->setDescription($request['description'])
                ->setLink($request['link']);
            $this->em->persist($literature);

            $topicLiterature = new TopicLiterature();
            $topicLiterature
                ->setLiterature($literature)
                ->setTopic($topic);
            $this->em->persist($topicLiterature);

            $this->em->flush();
            return $this->respondWithSuccess("Литература успешно добавлена");
        } catch (Exception) {
            return $this->respondValidationError();
        }
    }

    /**
     * Получение объекта литературы
     * @OA\Response(
     *     response=200,
     *     description="HTTP_OK",
     *     @OA\JsonContent(ref="#/components/schemas/LiteratureView")
     * )
     * @OA\Response(
     *     response=403,
     *     description="Доступ запрещён"
     * )
     * @OA\Response(
     *     response=404,
     *     description="Литература не найдена"
     * )
     */
    #[Route('/{literatureId}',
        name: 'get_by_id',
        requirements: ['literatureId' => '\d+'],
        methods: ['GET'])
    ]
    public function getLiterature(
        TopicLiteraturePreviewer $topicLiteraturePreviewer,
        int                      $literatureId
    ): JsonResponse
    {
        $literature = $this->literatureRepository->find($literatureId);
        if (!$literature) {
            return $this->respondNotFound("Литература не найдена");
        }

        return $this->response($topicLiteraturePreviewer->preview($literature));
    }

    /**
     * Изменение полей литературы
     * @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *         @OA\Property(property="name", nullable=false, ref="#/components/schemas/LiteratureView/properties/name"),
     *         @OA\Property(property="link", nullable=true, ref="#/components/schemas/LiteratureView/properties/link"),
     *         @OA\Property(
     *             property="topic_ids",
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Topic/properties/id")
     *         ),
     *         @OA\Property(property="image_base64", ref="#/components/schemas/LiteratureView/properties/imageBase64"),
     *     )
     * )
     * @OA\Response(
     *     response=200,
     *     description="Литература обновлена успешно"
     * )
     *
     * @OA\Response(
     *     response=403,
     *     description="Доступ запрещен"
     * )
     * @OA\Response(
     *     response=404,
     *     description="Литература не найдена"
     * )
     * @OA\Response(
     *     response=422,
     *     description="Неверные данные"
     * )
     */
    #[Route('/{literatureId}', name: 'put_by_id', requirements: ['literatureId' => '\d+'], methods: ['PUT'])]
    public function upLiterature(Request                   $request,
                                 int                       $literatureId,
                                 TopicLiteratureRepository $topicLiteratureRepository,
                                 TopicRepository           $topicRepository): JsonResponse
    {
        // TODO: Есть возможность задать пустой массив с топиками,
        //  тогда литература будет без темы. Стоит оставлять?

        $literature = $this->literatureRepository->find($literatureId);
        if (!$literature) {
            return $this->respondNotFound("Литература не найдена");
        }

        $request = $request->request->all();

        try {
            if (isset($request['name'])) {
                $literature->setName($request['name']);
            }
            if (isset($request['description'])) {
                $literature->setDescription($request['description']);
            }
            if (isset($request['link'])) {
                $literature->setLink($request['link']);
            }

            if (isset($request['topic_ids'])) {
                $topicIds = $request['topic_ids'];

                // Проверка на существование топиков
                $topics = [];
                foreach ($topicIds as $topicId) {
                    $topic = $topicRepository->find($topicId);

                    if (!$topic) {
                        return $this->respondNotFound("Тип с id: $topicId не найден");
                    }

                    $topics[] = $topic;
                }

                // Удаление всех существующих TopicLiterature если они не подходят
                $topicLiteratures = $topicLiteratureRepository->findBy([
                    "literature" => $literature
                ]);
                $notFoundTopicLiteratures = $topicLiteratures
                    ? array_filter(
                        $topicLiteratures,
                        fn(TopicLiterature $topicLiterature): bool
                            => !in_array($topicLiterature->getTopic(), $topics)
                    )
                    : [];
                foreach ($notFoundTopicLiteratures as $notFoundTopicLiterature) {
                    $this->em->remove($notFoundTopicLiterature);
                }

                // Создание новых связей литературы и топиков
                foreach ($topics as $topic) {
                    $topicLiterature = $topicLiteratureRepository->findOneBy([
                        "literature" => $literature,
                        "topic" => $topic
                    ]);

                    if (!$topicLiterature) {
                        $topicLiterature = new TopicLiterature();
                        $topicLiterature
                            ->setLiterature($literature)
                            ->setTopic($topic)
                        ;

                        $this->em->persist($topicLiterature);
                    }
                }


            }

            $this->em->flush();

            return $this->respondWithSuccess("Литература обновлена успешно");
        } catch (Exception) {
            return $this->respondValidationError();
        }
    }

    /**
     * Delete literature
     * @OA\Response(
     *     response=200,
     *     description="Литература успешно удалена"
     * )
     * @OA\Response(
     *     response=403,
     *     description="Доступ запрещён"
     * )
     * @OA\Response(
     *     response=404,
     *     description="Литература не найдена"
     * )
     */
    #[Route('/{literatureId}', name: 'delete_by_id', requirements: ['literatureId' => '\d+'], methods: ['DELETE'])]
    public function delLiterature(int $literatureId): JsonResponse
    {
        $literature = $this->literatureRepository->find($literatureId);
        if (!$literature) {
            return $this->respondNotFound("Литература не найдена");
        }

        $this->em->remove($literature);
        $this->em->flush();

        return $this->respondWithSuccess("Литература успешно удалена");
    }
}
