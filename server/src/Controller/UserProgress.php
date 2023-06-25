<?php

namespace App\Controller;

use App\Entity\Level;
use App\Entity\TaskMark;
use App\Previewer\LevelPreviewer;
use App\Previewer\UserPreviewer;
use App\Repository\LevelRepository;
use App\Repository\TaskMarkRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Progress")
 * @Security(name="Bearer")
 */
#[Route('/progress', name: 'progress_')]
class UserProgress extends ApiController
{
    private UserRepository $userRepository;
    private EntityManagerInterface $em;
    private TaskMarkRepository $taskMarkRepository;
    private LevelRepository $levelRepository;

    public function __construct(
        UserRepository         $userRepository,
        TaskMarkRepository     $taskMarkRepository,
        LevelRepository        $levelRepository,
        EntityManagerInterface $em
    )
    {
        $this->userRepository = $userRepository;
        $this->taskMarkRepository = $taskMarkRepository;
        $this->levelRepository = $levelRepository;
        $this->em = $em;
    }

    /**
     * Получение таблицы лидеров по очкам
     * @OA\Response(
     *     response=200,
     *     description="HTTP_OK",
     *     @OA\JsonContent(
     *          type="array",
     *          @OA\Items(
     *              @OA\Property(
     *                 property="user",
     *                 type="object",
     *                 description="The user details",
     *                 @OA\Property(property="id", ref="#/components/schemas/UserView/properties/id"),
     *                 @OA\Property(property="fio", ref="#/components/schemas/UserView/properties/fio"),
     *                 @OA\Property(property="avatar_name", ref="#/components/schemas/UserView/properties/avatarName"),
     *                 @OA\Property(property="avatar_base64", ref="#/components/schemas/UserView/properties/avatarBase64")
     *              ),
     *              @OA\Property(
     *                  property="scores",
     *                  type="integer",
     *                  description="Total scores"
     *              ),
     *              @OA\Property(
     *                 property="current_level",
     *                 type="object",
     *                 description="The current level details",
     *                 @OA\Property(property="name", ref="#/components/schemas/LevelView/properties/name"),
     *                 @OA\Property(property="need_scores", ref="#/components/schemas/LevelView/properties/need_scores")
     *              ),
     *              @OA\Property(
     *                 property="next_level",
     *                 type="object",
     *                 description="The next level details",
     *                 @OA\Property(property="name", ref="#/components/schemas/LevelView/properties/name"),
     *                 @OA\Property(property="need_scores", ref="#/components/schemas/LevelView/properties/need_scores")
     *              )
     *          )
     *      )
     * )
     * @OA\Response(
     *     response=403,
     *     description="Permission denied!"
     * )
     * @OA\Response(
     *     response=404,
     *     description="Scores not found"
     * )
     * @throws NonUniqueResultException
     */
    #[Route(
        '/leaders',
        name: 'get_users',
        methods: ['GET']
    )]
    public function getUsersProgress(
        UserPreviewer $userPreviewer,
        LevelPreviewer $levelPreviewer
    ): JsonResponse
    {
        $users = $this->userRepository->findAll();

        $result = [];
        foreach ($users as $user) {
            $taskMarks = $this->taskMarkRepository->findBy(
                ["user" => $user],
                ['updatedAt' => 'ASC']
            );
            $scores = array_map(
                fn(TaskMark $taskMark): int => $taskMark->getRating(),
                $taskMarks
            );

            $userPreview = $userPreviewer->previewFioAndAvatar($user);

            $sumScores = array_sum($scores);
            $level = $this->levelRepository->findLevelByScores($sumScores);
            $nextLevel = $this->levelRepository->findNextLevelByScores($sumScores);

            $result[] = array(
                "user" => $userPreview,
                "scores" => $sumScores,
                "current_level" => $levelPreviewer->previewWithoutId($level),
                "next_level" => $levelPreviewer->previewWithoutId($nextLevel)
            );
        }

        // Функция сравнения для usort()
        usort($result, function ($a, $b) {
            if ($a["scores"] == $b["scores"]) {
                return 0;
            }
            return ($a["scores"] > $b["scores"]) ? -1 : 1;
        });

        return $this->response($result);
    }

    /**
     * Таблица всех своих набранных очков
     * @OA\Response(
     *     response=200,
     *     description="HTTP_OK",
     *     @OA\JsonContent(
     *              @OA\Property(
     *                  property="scores",
     *                  type="integer",
     *                  description="Total scores"
     *              ),
     *              @OA\Property(
     *                 property="current_level",
     *                 type="object",
     *                 description="The current level details",
     *                 @OA\Property(property="name", ref="#/components/schemas/LevelView/properties/name"),
     *                 @OA\Property(property="need_scores", ref="#/components/schemas/LevelView/properties/need_scores")
     *              ),
     *              @OA\Property(
     *                 property="next_level",
     *                 type="object",
     *                 description="The next level details",
     *                 @OA\Property(property="name", ref="#/components/schemas/LevelView/properties/name"),
     *                 @OA\Property(property="need_scores", ref="#/components/schemas/LevelView/properties/need_scores")
     *              )
     *      )
     * )
     * @OA\Response(
     *     response=403,
     *     description="Permission denied!"
     * )
     * @OA\Response(
     *     response=404,
     *     description="Scores not found"
     * )
     * @throws NonUniqueResultException
     */
    #[Route(
        '/self',
        name: 'get_self',
        methods: ['GET']
    )]
    public function getSelfProgress(
        LevelPreviewer $levelPreviewer
    ): JsonResponse
    {
        $user = $this->getUserEntity($this->userRepository);
        $taskMarks = $this->taskMarkRepository->findBy(
            ["user" => $user],
            ['updatedAt' => 'ASC']
        );
        $scores = array_map(
            fn(TaskMark $taskMark): int => $taskMark->getRating(),
            $taskMarks
        );

        $sumScores = array_sum($scores);
        $level = $this->levelRepository->findLevelByScores($sumScores);
        $nextLevel = $this->levelRepository->findNextLevelByScores($sumScores);

        return $this->response(
            array(
                "scores" => $sumScores,
                "current_level" => $levelPreviewer->previewWithoutId($level),
                "next_level" => $levelPreviewer->previewWithoutId($nextLevel)
            )
        );
    }

    /**
     * Получение всех существующих уровней
     * @OA\Response(
     *     response=200,
     *     description="HTTP_OK",
     *     @OA\JsonContent(
     *          type="array",
     *          @OA\Items(ref="#/components/schemas/LevelView")
     *      )
     * )
     * @OA\Response(
     *     response=403,
     *     description="Permission denied!"
     * )
     * @OA\Response(
     *     response=404,
     *     description="Scores not found"
     * )
     */
    #[Route(
        '/levels',
        name: 'get_levels',
        methods: ['GET']
    )]
    public function getLevels(
        LevelPreviewer $levelPreviewer
    ): JsonResponse
    {
        $levels = $this->levelRepository->findAll();
        $levelPreviews = array_map(
            fn(Level $level): array => $levelPreviewer->preview($level),
            $levels
        );

        return $this->response($levelPreviews);
    }
}