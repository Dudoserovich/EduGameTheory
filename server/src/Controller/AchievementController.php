<?php

namespace App\Controller;

use App\Entity\Achievement;
use App\Entity\UserAchievement;
use App\Previewer\AchievementPreviewer;
use App\Previewer\UserAchievementPreviewer;
use App\Repository\AchievementRepository;
use App\Repository\UserAchivementRepository;
use App\Repository\UserRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Validation;

/**
 * @OA\Tag(name="Achievement")
 * @Security(name="Bearer")
 **/
#[Route('/achievements', name: 'achievements_')]
class AchievementController extends ApiController
{
    private AchievementRepository $achievementRepository;
    private EntityManagerInterface $em;
    private UserAchivementRepository $userAchivementRepository;
    private UserRepository $userRepository;

    public function __construct(
        UserRepository           $userRepository,
        AchievementRepository    $achievementRepository,
        UserAchivementRepository $userAchivementRepository,
        EntityManagerInterface   $em
    )
    {
        $this->userRepository = $userRepository;
        $this->achievementRepository = $achievementRepository;
        $this->userAchivementRepository = $userAchivementRepository;
        $this->em = $em;
    }

//    /**
//     * Add new achievement
//     * @OA\RequestBody(
//     *     description="P.S. svg загрузить не получится",
//     *     required=true,
//     *     @OA\MediaType(
//     *         mediaType="multipart/form-data",
//     *         @OA\Schema(
//     *             @OA\Property(
//     *                  property="name",
//     *                  ref="#/components/schemas/AchievementView/properties/name"
//     *             ),
//     *             @OA\Property(
//     *                  property="description",
//     *                  ref="#/components/schemas/AchievementView/properties/description"
//     *             ),
//     *             @OA\Property(
//     *                  property="imageFile",
//     *                  nullable=false,
//     *                  ref="#/components/schemas/AchievementView/properties/imageFile"
//     *             ),
//     *         )
//     *     )
//     * )
//     * @OA\Response(
//     *     response=200,
//     *     description="Achievement added successfully"
//     * )
//     * @OA\Response(
//     *     response=403,
//     *     description="Permission denied!"
//     * )
//     * @OA\Response(
//     *     response=422,
//     *     description="Data no valid"
//     * )
//     */
//    #[Route(name: 'post', methods: ['POST'])]
//    public function postAchievement(
//        Request $request,
//        FileUploader $fileUploader
//    ): JsonResponse
//    {
//        $jsonRequest = $request->request->all();
//
//        /**
//         * @var $imageFile File
//         */
//        $imageFile = $request->files->get('imageFile');
//        if (!$imageFile) {
//            return $this->respondValidationError("File for achievement not transferred");
//        }
//
//        // Проверяем, что файл - изображение
//        if (!$fileUploader->isImage($imageFile))
//            return $this->respondValidationError("Incorrect image type" . $imageFile->getMimeType());
//
//        $achievementByName = $this->achievementRepository
//            ->findOneBy(["name" => $jsonRequest['name']]);
//
//        if (!$achievementByName) {
//            try {
//                $achievement = new Achievement();
//
//                $achievement
//                    ->setImageFile($imageFile)
//                    ->setName($jsonRequest['name'])
//                    ->setDescription($jsonRequest['description']);
//
//                $this->em->persist($achievement);
//                $this->em->flush();
//
//                return $this->respondWithSuccess("Achievement added successfully");
//            } catch (Exception $e) {
//                return $this->respondValidationError();
//            }
//        } else {
//            return $this->respondValidationError("Achievement with this name is already exists");
//        }
//    }

    /**
     * Получение конкретного объекта достижения
     * @OA\Response(
     *     response=200,
     *     description="HTTP_OK",
     *     @OA\JsonContent(
     *         @OA\Property(property="name", ref="#/components/schemas/AchievementView/properties/name"),
     *         @OA\Property(property="description", ref="#/components/schemas/AchievementView/properties/description"),
     *         @OA\Property(property="imageHref", ref="#/components/schemas/AchievementView/properties/imageHref")
     *     )
     * )
     * @OA\Response(
     *     response=403,
     *     description="Permission denied!"
     * )
     * @OA\Response(
     *     response=404,
     *     description="Achievement not found"
     * )
     */
    #[Route('/{achievementId}',
        name: 'get_achievement',
        requirements: ['achievementId' => '\d+'],
        methods: ['GET'])
    ]
    public function getAchievement(
        int                  $achievementId,
        AchievementPreviewer $achievementPreviewer): JsonResponse
    {
        $achievement = $this->achievementRepository->find($achievementId);
        if (!$achievement) {
            return $this->respondNotFound("Achievement not found");
        }

        return $this->response($achievementPreviewer->preview($achievement));
    }

//    /**
//     * Change achievement (this is no REST style :( )
//     * @OA\RequestBody(
//     *     description="P.S. svg загрузить не получится",
//     *     required=true,
//     *     @OA\MediaType(
//     *         mediaType="multipart/form-data",
//     *         @OA\Schema(
//     *             @OA\Property(
//     *                  property="name",
//     *                  ref="#/components/schemas/AchievementView/properties/name"
//     *             ),
//     *             @OA\Property(
//     *                  property="description",
//     *                  ref="#/components/schemas/AchievementView/properties/description"
//     *             ),
//     *             @OA\Property(
//     *                  property="imageFile",
//     *                  nullable=false,
//     *                  ref="#/components/schemas/AchievementView/properties/imageFile"
//     *             ),
//     *         )
//     *     )
//     * )
//     * @OA\Response(
//     *     response=200,
//     *     description="Achievement updated successgully"
//     * )
//     * * @OA\Response(
//     *     response=403,
//     *     description="Permission denied!"
//     * )
//     * @OA\Response(
//     *     response=422,
//     *     description="Data no valid"
//     * )
//     *
//     * @OA\Response(
//     *     response=404,
//     *     description="Achievement not found"
//     * )
//     */
//    #[Route('/{achievementId}',
//        name: 'put_by_id',
//        requirements: ['achievementId' => '\d+'],
//        methods: ['POST']
//    )]
//    public function upAchievement(Request $request,
//                                      int $achievementId,
//                                      FileUploader $fileUploader): JsonResponse
//    {
//        $achievement = $this->achievementRepository
//            ->find($achievementId);
//
//        if (!$achievement) {
//            return $this->respondNotFound("Achievement not found");
//        }
//
//        $jsonRequest = $request->request->all();
//
//        try {
//            if (isset($jsonRequest['name'])) {
//                if ($this->achievementRepository->findOneBy(['name' => $jsonRequest['name']])) {
//                    return $this->respondValidationError('Achievement with this name is already exist');
//                }
//
//                $achievement->setName($jsonRequest['name']);
//            }
//
//            if (isset($jsonRequest['description'])) {
//                $achievement->setDescription($jsonRequest['description']);
//            }
//
//            /**
//             * @var $imageFile File
//             */
//            $imageFile = $request->files->get('imageFile');
//            if (isset($imageFile)) {
//                // Проверяем, что файл - изображение
//                if (!$fileUploader->isImage($imageFile))
//                    return $this->respondValidationError("Incorrect image type");
//                else {
//                    $fileUploader->delete($achievement->getImageName());
//                    $achievement->setImageFile($imageFile);
//                }
//            }
//
//            $this->em->persist($achievement);
//            $this->em->flush();
//
//            return $this->respondWithSuccess("Achievement updated successfully");
//        } catch (Exception) {
//            return $this->respondValidationError();
//        }
//    }

    /**
     * Получение всех существующий достижений
     * @OA\Response(
     *     response=200,
     *     description="HTTP_OK",
     *     @OA\JsonContent(
     *         type="array",
     *         @OA\Items(
     *            @OA\Property(property="name", ref="#/components/schemas/AchievementView/properties/name"),
     *            @OA\Property(property="description", ref="#/components/schemas/AchievementView/properties/description"),
     *            @OA\Property(property="imageHref", ref="#/components/schemas/AchievementView/properties/imageHref")
     *         )
     *     )
     * )
     * @OA\Response(
     *     response=403,
     *     description="Permission denied"
     * )
     */
    #[Route(name: 'get', methods: ['GET'])]
    public function getAchievements(
        AchievementPreviewer $achievementPreviewer): JsonResponse
    {
        $achievements = $this->achievementRepository->findAll();

        $achievementPreviews = array_map(
            fn(Achievement $achievement): array => $achievementPreviewer->preview($achievement),
            $achievements
        );

        return $this->response($achievementPreviews);
    }

    /**
     * Получение всех достижений текущего пользователя (даже ещё не полученных)
     * @OA\Response(
     *     response=200,
     *     description="HTTP_OK",
     *     @OA\JsonContent(
     *         type="array",
     *         @OA\Items(
     *             @OA\Property(
     *                 property="id",
     *                 type="integer",
     *                 description="The ID of the achievement"
     *             ),
     *             @OA\Property(
     *                 property="achievement",
     *                 type="object",
     *                 description="The achievement details",
     *                 @OA\Property(
     *                     property="id",
     *                     type="integer",
     *                     description="The ID of the achievement"
     *                 ),
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                     description="The name of the achievement"
     *                 ),
     *                 @OA\Property(
     *                     property="description",
     *                     type="string",
     *                     description="The description of the achievement"
     *                 ),
     *                 @OA\Property(
     *                     property="image_href",
     *                     type="string",
     *                     description="The URL of the image for the achievement"
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="progress",
     *                 type="array",
     *                 description="The progress towards the achievement",
     *                 @OA\Items(
     *                     type="integer"
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="achievement_date",
     *                 type="string",
     *                 format="date-time",
     *                 description="The date and time when the achievement was earned"
     *             )
     *         )
     *     )
     * )
     * @OA\Response(
     *     response=403,
     *     description="Permission denied"
     * )
     */
    #[Route(
        '/users/self',
        name: 'get_self',
        methods: ['GET']
    )]
    public function getSelfAchievements(
        AchievementPreviewer     $achievementPreviewer,
        UserAchievementPreviewer $userAchievementPreviewer
    ): JsonResponse
    {
        $user = $this->getUserEntity($this->userRepository);
        $userAchievements = $this->userAchivementRepository->findBy(["user" => $user]);

        $achievementPreviews = array_map(
            fn(UserAchievement $userAchievement): array => $userAchievementPreviewer->previewWithoutUser($userAchievement),
            $userAchievements
        );

        return $this->response($achievementPreviews);
    }

    /**
     * Получение только всех полученных достижений текущего пользователя
     * @OA\Response(
     *     response=200,
     *     description="HTTP_OK",
     *     @OA\JsonContent(
     *         type="array",
     *         @OA\Items(
     *             @OA\Property(
     *                 property="id",
     *                 type="integer",
     *                 description="The ID of the achievement"
     *             ),
     *             @OA\Property(
     *                 property="achievement",
     *                 type="object",
     *                 description="The achievement details",
     *                 @OA\Property(
     *                     property="id",
     *                     type="integer",
     *                     description="The ID of the achievement"
     *                 ),
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                     description="The name of the achievement"
     *                 ),
     *                 @OA\Property(
     *                     property="description",
     *                     type="string",
     *                     description="The description of the achievement"
     *                 ),
     *                 @OA\Property(
     *                     property="image_href",
     *                     type="string",
     *                     description="The URL of the image for the achievement"
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="progress",
     *                 type="array",
     *                 description="The progress towards the achievement",
     *                 @OA\Items(
     *                     type="integer"
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="achievement_date",
     *                 type="string",
     *                 format="date-time",
     *                 description="The date and time when the achievement was earned"
     *             )
     *         )
     *     )
     * )
     * @OA\Response(
     *     response=403,
     *     description="Permission denied"
     * )
     */
    #[Route(
        '/users/self/completed',
        name: 'get_self_completed',
        methods: ['GET']
    )]
    public function getSelfCompletedAchievements(
        AchievementPreviewer     $achievementPreviewer,
        UserAchievementPreviewer $userAchievementPreviewer
    ): JsonResponse
    {
        $user = $this->getUserEntity($this->userRepository);
        $userAchievements = $this->userAchivementRepository->findByUserAndNotNullAchieve($user);

        $achievementPreviews = array_map(
            fn(UserAchievement $userAchievement): array => $userAchievementPreviewer->previewWithoutUser($userAchievement),
            $userAchievements
        );

        return $this->response($achievementPreviews);
    }

    /**
     * Получение всех достижений пользователя по id (даже ещё не полученных)
     * @OA\Response(
     *     response=200,
     *     description="HTTP_OK",
     *     @OA\JsonContent(
     *         type="array",
     *         @OA\Items(
     *             @OA\Property(
     *                 property="id",
     *                 type="integer",
     *                 description="The ID of the achievement"
     *             ),
     *             @OA\Property(
     *                 property="achievement",
     *                 type="object",
     *                 description="The achievement details",
     *                 @OA\Property(
     *                     property="id",
     *                     type="integer",
     *                     description="The ID of the achievement"
     *                 ),
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                     description="The name of the achievement"
     *                 ),
     *                 @OA\Property(
     *                     property="description",
     *                     type="string",
     *                     description="The description of the achievement"
     *                 ),
     *                 @OA\Property(
     *                     property="image_href",
     *                     type="string",
     *                     description="The URL of the image for the achievement"
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="progress",
     *                 type="array",
     *                 description="The progress towards the achievement",
     *                 @OA\Items(
     *                     type="integer"
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="achievement_date",
     *                 type="string",
     *                 format="date-time",
     *                 description="The date and time when the achievement was earned"
     *             )
     *         )
     *     )
     * )
     * @OA\Response(
     *     response=403,
     *     description="Permission denied"
     * )
     */
    #[Route(
        '/users/{userId}',
        name: 'get_by_id',
        requirements: ['taskId' => '\d+'],
        methods: ['GET']
    )]
    public function getAchievementsById(
        UserAchievementPreviewer $userAchievementPreviewer,
        int                      $userId
    ): JsonResponse
    {
        $user = $this->userRepository->find($userId);
        $userAchievements = $this->userAchivementRepository->findBy(["user" => $user]);

        $achievementPreviews = array_map(
            fn(UserAchievement $userAchievement): array => $userAchievementPreviewer->previewWithoutUser($userAchievement),
            $userAchievements
        );

        return $this->response($achievementPreviews);
    }

    /**
     * Получение только всех полученных достижений пользователя по id
     * @OA\Response(
     *     response=200,
     *     description="HTTP_OK",
     *     @OA\JsonContent(
     *         type="array",
     *         @OA\Items(
     *             @OA\Property(
     *                 property="id",
     *                 type="integer",
     *                 description="The ID of the achievement"
     *             ),
     *             @OA\Property(
     *                 property="achievement",
     *                 type="object",
     *                 description="The achievement details",
     *                 @OA\Property(
     *                     property="id",
     *                     type="integer",
     *                     description="The ID of the achievement"
     *                 ),
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                     description="The name of the achievement"
     *                 ),
     *                 @OA\Property(
     *                     property="description",
     *                     type="string",
     *                     description="The description of the achievement"
     *                 ),
     *                 @OA\Property(
     *                     property="image_href",
     *                     type="string",
     *                     description="The URL of the image for the achievement"
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="progress",
     *                 type="array",
     *                 description="The progress towards the achievement",
     *                 @OA\Items(
     *                     type="integer"
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="achievement_date",
     *                 type="string",
     *                 format="date-time",
     *                 description="The date and time when the achievement was earned"
     *             )
     *         )
     *     )
     * )
     * @OA\Response(
     *     response=403,
     *     description="Permission denied"
     * )
     */
    #[Route(
        '/users/{userId}/completed',
        name: 'get_completed_by_id',
        requirements: ['taskId' => '\d+'],
        methods: ['GET']
    )]
    public function getAchievementsCompletedById(
        UserAchievementPreviewer $userAchievementPreviewer,
        int                      $userId
    ): JsonResponse
    {
        $user = $this->userRepository->find($userId);
        $userAchievements = $this->userAchivementRepository->findByUserAndNotNullAchieve($user);

        $achievementPreviews = array_map(
            fn(UserAchievement $userAchievement): array => $userAchievementPreviewer->previewWithoutUser($userAchievement),
            $userAchievements
        );

        return $this->response($achievementPreviews);
    }

//    /**
//     * Delete achievement
//     * @OA\Response(
//     *     response=200,
//     *     description="Achievement deleted successfully"
//     * )
//     * @OA\Response(
//     *     response=403,
//     *     description="Permission denied!"
//     * )
//     * @OA\Response(
//     *     response=404,
//     *     description="Achievement not found"
//     * )
//     */
//    #[Route('/{achievementId}',
//        name: 'delete_by_id',
//        requirements: ['achievementId' => '\d+'],
//        methods: ['DELETE']
//    )]
//    public function delAchievement(int $achievementId): JsonResponse
//    {
//        $achievement = $this->achievementRepository->find($achievementId);
//        if (!$achievement) {
//            return $this->respondNotFound("Achievement not found");
//        }
//
//        // Автоматом подписчиком Достижений удаляется и файл
//        $this->em->remove($achievement);
//        $this->em->flush();
//
//        return $this->respondWithSuccess("Achievement deleted successfully");
//    }
}
