<?php

namespace App\Controller;

use App\Repository\AchievementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Annotation\Route;
use Exception;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Test")
 * @Security(name="Bearer")
 **/
#[Route('/tests', name: 'tests_')]
class TestController extends ApiController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    // TODO: стоит добавить
    //  в передаваемые параметры type и message.
    // TODO: topic стоит передавать в теле и пусть это будет массив строк
    /**
     * Публикация вашей темы
     * @param HubInterface $hub
     * @param Request $request
     * @return JsonResponse
     *
     * @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *         example={
     *              "topics": {"/news"},
     *              "message": "Добавлен сервис, позволяющий получать фронтенд части сообщения с бэка без запроса",
     *              "type": "news"
     *         },
     *         @OA\Property(property="topics", type="array", @OA\Items(type="string")),
     *         @OA\Property(property="message", type="string"),
     *         @OA\Property(property="type", type="string")
     *     )
     * )
     *
     * @OA\Response(
     *     response=200,
     *     description="OK"
     * )
     *
     * @OA\Response(
     *     response=403,
     *     description="Доступ запрещён"
     * )
     *
     */
    #[Route('/publish',
        name: 'new_publish',
        methods: ['POST'])]
    public function publish(HubInterface $hub,
                            Request $request): JsonResponse
    {
        $request = $request->request->all();

        $update = new Update(
            topics: $request['topics'],
            data: json_encode(['message' => $request['message']]),
            type: $request['type']
        );

        $hub->publish($update);

        return new JsonResponse(['status' => 'published!']);
    }
}