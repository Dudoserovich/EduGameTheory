<?php

namespace App\Controller;

use App\Entity\Term;
use App\Repository\TermRepository;
use App\Repository\TopicRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Previewer\TermPreviewer;

use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Term")
 * @Security(name="Bearer")
 */
#[Route('/terms', name: 'terms_')]
class TermController extends ApiController
{
    private TermRepository $termRepository;
    private EntityManagerInterface $em;

    public function __construct(TermRepository $termRepository, EntityManagerInterface $em)
    {
        $this->termRepository = $termRepository;
        $this->em = $em;
    }

    /**
     * Получение всех терминов отсортированных по названию
     * @OA\Response(
     *     response=200,
     *     description="HTTP_OK",
     *     @OA\JsonContent(
     *         type="array",
     *         @OA\Items(ref="#/components/schemas/TermView")
     *     )
     * )
     * @OA\Response(
     *     response=403,
     *     description="Permission deinied"
     * )
     */
//    #[Get(deprecated: true)]
    #[Route(name: 'get', methods: ['GET'])]
    public function getTerms(TermPreviewer $termPreviewer): JsonResponse
    {
        $termPreviewers = array_map(
            fn(Term $term): array => $termPreviewer->preview($term),
            $this->termRepository->findBy([], ["name" => "ASC"])
        );

        return $this->response($termPreviewers);
    }

    /**
     * Добавление нового термина
     * @OA\RequestBody (
     *     required=true,
     *     @OA\JsonContent(
     *         example={"name": "Игрок", "description": "Одна из сторон в игровой ситуации", "topic_id": 1},
     *         @OA\Property(property="name", ref="#/components/schemas/Term/properties/name"),
     *         @OA\Property(property="description", ref="#/components/schemas/Term/properties/description"),
     *         @OA\Property(property="topic_id", ref="#/components/schemas/Topic/properties/id")
     *     )
     * )
     * @OA\Response(
     *     response=200,
     *     description="Термин добавлен успешно"
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
    public function postTerm(
        Request $request,
        TopicRepository $topicRepository
    ): JsonResponse
    {
        $request = $request->request->all();

        $this->setSoftDeleteable(false);
        $term = $this->termRepository->findOneBy(['name' => $request['name']]);
        if ($term)
            if (!$term->getDeletedAt())
                return $this->respondValidationError('Термин с таким названием уже создан');
            else $term->setDeletedAt(null);

        $topic = $topicRepository->find($request['topic_id']);
        if (!$topic) {
            return $this->respondNotFound("Тип не найден");
        }

        $term = $term ?? new Term();
        try {
            $term
                ->setName($request['name'])
                ->setDescription($request['description'])
                ->setTopic($topic)
            ;
            $this->em->persist($term);
            $this->em->flush();
            return $this->respondWithSuccess("Термин добавлен успешно");
        } catch (Exception) {
            return $this->respondValidationError();
        }
    }

    /**
     * Получение объекта термина
     * @OA\Response(
     *     response=200,
     *     description="HTTP_OK",
     *     @OA\JsonContent(ref="#/components/schemas/TermView")
     * )
     * @OA\Response(
     *     response=403,
     *     description="Доступ запрещён"
     * )
     * @OA\Response(
     *     response=404,
     *     description="Термин не найден"
     * )
     */
    #[Route('/{termId}', name: 'get_by_id', requirements: ['termId' => '\d+'], methods: ['GET'])]
    public function getTerm(
        TermPreviewer $termPreviewer,
        int $termId
    ): JsonResponse
    {
        $term = $this->termRepository->find($termId);
        if (!$term) {
            return $this->respondNotFound("Термин не найден");
        }

        return $this->response($termPreviewer->preview($term));
    }

    /**
     * Изменение полей термина
     * @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *         @OA\Property(property="name", nullable=true, ref="#/components/schemas/Term/properties/name"),
     *         @OA\Property(property="description", nullable=true, ref="#/components/schemas/Term/properties/description"),
     *         @OA\Property(property="topic_id", ref="#/components/schemas/Topic/properties/id")
     *     )
     * )
     * @OA\Response(
     *     response=200,
     *     description="Термин успешно обновлён"
     * )
     * * @OA\Response(
     *     response=403,
     *     description="Доступ запрещён"
     * )
     * @OA\Response(
     *     response=404,
     *     description="Термин не наёден"
     * )
     * @OA\Response(
     *     response=422,
     *     description="Неверные данные"
     * )
     */
    #[Route('/{termId}', name: 'put_by_id', requirements: ['termId' => '\d+'], methods: ['PUT'])]
    public function upTerm(
        Request $request,
        int $termId,
        TopicRepository $topicRepository
    ): JsonResponse
    {
        $term = $this->termRepository->find($termId);
        if (!$term) {
            return $this->respondNotFound("Термин не найден");
        }

        $request = $request->request->all();

        try {
            if (isset($request['name'])) {
                $term->setName($request['name']);
            }
            if (isset($request['description'])) {
                $term->setDescription($request['description']);
            }
            if (isset($request['topic_id'])) {
                $newTopic = $topicRepository->find($request['topic_id']);
                if (!$newTopic) {
                    return $this->respondNotFound("Тип не найден");
                }

                $term->setTopic($newTopic);
            }

            $this->em->flush();

            return $this->respondWithSuccess("Термин успешно обновлён");
        } catch (Exception) {
            return $this->respondValidationError();
        }
    }

    /**
     * Удаление термина
     * @OA\Response(
     *     response=200,
     *     description="Термин удалён успешно"
     * )
     * @OA\Response(
     *     response=403,
     *     description="Доступ запрещён"
     * )
     * @OA\Response(
     *     response=404,
     *     description="Термин не найден"
     * )
     */
    #[Route('/{termId}', name: 'delete_by_id', requirements: ['termId' => '\d+'], methods: ['DELETE'])]
    public function delTerm(int $termId): JsonResponse
    {
        $term = $this->termRepository->find($termId);
        if (!$term) {
            return $this->respondNotFound("Термин не найден");
        }

        $this->em->remove($term);
        $this->em->flush();

        return $this->respondWithSuccess("Термин удалён успешно");
    }
}
