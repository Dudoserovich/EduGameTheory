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
     *     description="Permission deinied"
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
     *     description="Permission deinied"
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
            return $this->respondNotFound("Education not found");
        }

        $user = $this->getUserEntity($this->userRepository);
        return $this->response($educationPreviewer->previewByEduAndUser($education, $user));
    }

    /**
     * Обновление статуса прохождения блока
     * @OA\Response(
     *     response=200,
     *     description="Block done"
     * )
     * @OA\Response(
     *     response=403,
     *     description="Permission deinied"
     * )
     * @OA\Response(
     *     response=404,
     *     description="Not found"
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
            return $this->respondNotFound("Education not found");
        }

        if (!$block) {
            return $this->respondNotFound("Blocks not found");
        }

        $user = $this->getUserEntity($this->userRepository);
        $userBlock = $this->userEduTasksRepository->findOneBy([
            "user" => $user,
            "eduTasks" => $block
        ]);

        if (!$userBlock) {
            $this->respondNotFound('Block not found');
        } else {
            $userBlock->setSuccess(true);
            $this->em->flush();
        }

        return $this->respondWithSuccess("Block done");
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
            return $this->respondNotFound("Education not found");
        }

        if (!$blocks) {
            return $this->respondNotFound("Blocks not found");
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
     *     description="Permission deinied"
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
            return $this->respondNotFound("Education not found");
        }

        if (!$block) {
            return $this->respondNotFound("Block not found");
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