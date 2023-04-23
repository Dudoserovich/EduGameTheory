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

    /**
     * Publish your topic
     * @param HubInterface $hub
     * @param string $topic
     * @param Request $request
     * @return JsonResponse
     *
     * @OA\Parameter(
     *     name="topic",
     *     in="path",
     *     description="topic",
     *     required=true,
     *     example="/news"
     * )
     *
     * @OA\Response(
     *     response=200,
     *     description="OK"
     * )
     *
     * @OA\Response(
     *     response=403,
     *     description="Permission denied"
     * )
     *
     */
    #[Route('/publish/{topic}',
        name: 'new_publish',
        requirements: ['topic' => '[/A-z0-9]+'],
        methods: ['POST'])]
    public function publish(HubInterface $hub, string $topic, Request $request): JsonResponse
    {
        $update = new Update(
            '/news',
            json_encode(['message' => 'Добавлен сервис, 
                    позволяющий получать фронтенд части сообщения с бэка без запроса'])
        );

        $hub->publish($update);

        return new JsonResponse(['status' => 'published!']);
    }
}