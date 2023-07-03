<?php

namespace App\Controller;

use App\Entity\Education;
use App\Entity\EducationTasks;
use App\Entity\UserEducationTasks;
use App\Previewer\EducationPreviewer;
use App\Previewer\EducationTasksPreviewer;
use App\Previewer\UserEduTasksPreviewer;
use App\Repository\EducationRepository;
use App\Repository\EducationTasksRepository;
use App\Repository\UserEducationTasksRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Education")
 * @Security(name="Bearer")
 */
#[Route('/education', name: 'education_')]
class EducationController extends ApiController
{
    private EducationRepository $educationRepository;
    private EducationTasksRepository $educationTasksRepository;
    private EntityManagerInterface $em;
    private UserEducationTasksRepository $userEduTasksRepository;
    private UserRepository $userRepository;

    public function __construct(
        EducationRepository          $educationRepository,
        EducationTasksRepository     $educationTasksRepository,
        UserEducationTasksRepository $userEduTasksRepository,
        UserRepository               $userRepository,
        EntityManagerInterface       $em
    )
    {
        $this->educationRepository = $educationRepository;
        $this->educationTasksRepository = $educationTasksRepository;
        $this->userEduTasksRepository = $userEduTasksRepository;
        $this->userRepository = $userRepository;
        $this->em = $em;
    }

    /**
     * Получение всех существующих обучений отсортированных по названию
     * @OA\Response(
     *     response=200,
     *     description="HTTP_OK",
     *     @OA\JsonContent(
     *         type="array",
     *         @OA\Items(ref="#/components/schemas/EducationView")
     *     )
     * )
     * @OA\Response(
     *     response=403,
     *     description="Доступ запрещён"
     * )
     */
    #[Route(name: 'get', methods: ['GET'])]
    public function getEducations(
        EducationPreviewer $educationPreviewer
    ): JsonResponse
    {
        $educations = $this->educationRepository->findBy([], ["name" => "ASC"]);

        // Наверное, тут стоит возвращать обучение без текста заключения
        $user = $this->getUserEntity($this->userRepository);
        $educationPreviewers = array_map(
            fn(Education $education): array => $educationPreviewer->previewByEduAndUser($education, $user),
            $educations
        );

        return $this->response($educationPreviewers);
    }

    /**
     * Начать обучение
     * @OA\Response(
     *     response=200,
     *     description="HTTP_OK",
     *     @OA\JsonContent(
     *         type="array",
     *         @OA\Items(ref="#/components/schemas/EducationView")
     *     )
     * )
     * @OA\Response(
     *     response=403,
     *     description="Доступ запрещён"
     * )
     */
    #[Route(
        '/{eduId}/start',
        name: 'start',
        requirements: ['eduId' => '\d+'],
        methods: ['GET']
    )]
    public function getStartEducations(
        EducationPreviewer $educationPreviewer,
        int                $eduId
    ): JsonResponse
    {
        $education = $this->educationRepository->find($eduId);

        if (!$education) {
            return $this->respondNotFound("Обучение не найдено");
        }

        $user = $this->getUserEntity($this->userRepository);
        return $this->response($educationPreviewer->previewByEduAndUser($education, $user));
    }

    /**
     * Обновление статуса прохождения блока
     * @OA\Response(
     *     response=200,
     *     description="Статус пройденности блока обновлён"
     * )
     * @OA\Response(
     *     response=403,
     *     description="Доступ запрещён"
     * )
     * @OA\Response(
     *     response=404,
     *     description="Не найдено"
     * )
     */
    #[Route(
        '/{eduId}/{blockNumber}',
        name: 'set_success_block',
        requirements: [
            'eduId' => '\d+',
            'blockNumber' => '\d+'
        ],
        methods: ['PUT']
    )]
    public function setSuccessBlockEducations(
        int                     $eduId,
        int                     $blockNumber
    ): JsonResponse
    {
        $education = $this->educationRepository->find($eduId);
        $block = $this->educationTasksRepository->findOneBy([
            "edu" => $education,
            "blockNumber" => $blockNumber
        ]);

        if (!$education) {
            return $this->respondNotFound("Обучение не найдено");
        }

        if (!$block) {
            return $this->respondNotFound("Блок не найден");
        }

        $user = $this->getUserEntity($this->userRepository);
        $userBlock = $this->userEduTasksRepository->findOneBy([
            "user" => $user,
            "eduTasks" => $block
        ]);

        if (!$userBlock) {
            $this->respondNotFound('Блок не найден');
        } else {
            $userBlock->setSuccess(true);
            $this->em->flush();
        }

        return $this->respondWithSuccess("Статус пройденности блока обновлён");
    }

    /**
     * Получение всех блоков обучения
     * @OA\Response(
     *     response=200,
     *     description="HTTP_OK"
     * )
     * @OA\Response(
     *     response=403,
     *     description="Permission deinied"
     * )
     */
    #[Route(
        '/{eduId}/blocks',
        name: 'blocks',
        requirements: [
            'eduId' => '\d+'
        ],
        methods: ['GET']
    )]
    public function getAllBlockEducations(
        EducationTasksPreviewer $educationTasksPreviewer,
        UserEduTasksPreviewer   $userEduTasksPreviewer,
        int                     $eduId
    ): JsonResponse
    {
        $education = $this->educationRepository->find($eduId);
        $blocks = $this->educationTasksRepository->findBy([
            "edu" => $education
        ]);

        if (!$education) {
            return $this->respondNotFound("Обучение не найдено");
        }

        if (!$blocks) {
            return $this->respondNotFound("Блоки обучения не найдены");
        }

        $user = $this->getUserEntity($this->userRepository);
        $blocksPreviews = [];
        foreach ($blocks as $block) {
            $userBlock = $this->userEduTasksRepository->findOneBy([
                "user" => $user,
                "eduTasks" => $block
            ]);

            if (!$userBlock) {
                $userBlock = new UserEducationTasks();
                $userBlock
                    ->setUser($user)
                    ->setEduTasks($block)
                    ->setIsCurrentBlock(false)
                    ->setSuccess(false);

                $this->em->persist($userBlock);
                $this->em->flush();
            }

            $blocksPreviews[] = $userEduTasksPreviewer->previewWithoutUserAndEdu($userBlock);
        }

//        $blocksPreviews = array_map(
//            fn(EducationTasks $educationTasks): array => $educationTasksPreviewer->previewWithoutEdu($educationTasks),
//            $blocks
//        );

        return $this->response($blocksPreviews);
    }

    /**
     * Получение блока обучения
     * @OA\Response(
     *     response=200,
     *     description="HTTP_OK"
     * )
     * @OA\Response(
     *     response=403,
     *     description="Доступ запрещён"
     * )
     */
    #[Route(
        '/{eduId}/{blockNumber}',
        name: 'block',
        requirements: [
            'eduId' => '\d+',
            'blockNumber' => '\d+'
        ],
        methods: ['GET']
    )]
    public function getBlockEducations(
        UserEduTasksPreviewer $userEduTasksPreviewer,
        int                   $eduId,
        int                   $blockNumber
    ): JsonResponse
    {
        $education = $this->educationRepository->find($eduId);
        $block = $this->educationTasksRepository->findOneBy([
            "edu" => $education,
            "blockNumber" => $blockNumber
        ]);

        if (!$education) {
            return $this->respondNotFound("Обучение не найдено");
        }

        if (!$block) {
            return $this->respondNotFound("Блок обучения не найден");
        }

        $user = $this->getUserEntity($this->userRepository);
        // ставим блок как текущий
        $userBlock = $this->userEduTasksRepository->findOneBy([
            "user" => $user,
            "eduTasks" => $block
        ]);

        if (!$userBlock) {
            $userBlock = new UserEducationTasks();
            $userBlock
                ->setUser($user)
                ->setEduTasks($block)
                ->setIsCurrentBlock(true)
                ->setSuccess(false);
        } else {
            $userBlock->setIsCurrentBlock(true);
        }
        $this->em->persist($userBlock);
        $this->em->flush();

        // Ищем все блоки у пользователя, где необходимое обучение
        $allUserBlocks = $this->userEduTasksRepository->findBy(["user" => $user]);
        /**
         * @var UserEducationTasks[] $userBlocksByEdu
         */
        $userBlocksByEdu = array_filter(
            $allUserBlocks,
            fn(UserEducationTasks $userEduTasks): bool => $userEduTasks->getEduTasks()->getEdu() === $education
        );
        // Устанавливаем все прочие блоки как НЕ ТЕКУЩИЕ
        foreach ($userBlocksByEdu as $userBlockByEdu) {
            if ($userBlockByEdu->getId() !== $userBlock->getId()) {
                $userBlockByEdu->setIsCurrentBlock(false);
                $this->em->persist($userBlock);
                $this->em->flush();
            }
        }

        return $this->response($userEduTasksPreviewer->previewWithoutUserAndEdu($userBlock));
    }

    // TODO: Запрос на получение всех обучений
    //  с пометками о прохождении,
    //  если задание было пройдено

    // TODO: Запрос на получение текущего блока

    // TODO: Запрос на получение информации о конкретном обучении
    //  и степени его пройденности
}