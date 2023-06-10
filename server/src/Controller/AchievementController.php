<?php

namespace App\Controller;

use App\Entity\Achievement;
use App\Entity\UserAchievement;
use App\Previewer\AchievementPreviewer;
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
        UserRepository $userRepository,
        AchievementRepository $achievementRepository,
        UserAchivementRepository $userAchivementRepository,
        EntityManagerInterface $em
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
     * Get achievement object
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
        int $achievementId,
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
     * Get all achievements
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
     * Get all self achievements
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
    #[Route(
        '/self',
        name: 'get_self',
        methods: ['GET']
    )]
    public function getSelfAchievements(
        AchievementPreviewer $achievementPreviewer): JsonResponse
    {
        $user = $this->getUserEntity($this->userRepository);
        $userAchievements = $this->userAchivementRepository->findBy(["user" => $user]);

        $achievements = array_map(
            fn(UserAchievement $achievement): Achievement => $achievement->getAchievement(),
            $userAchievements
        );

        $achievementPreviews = array_map(
            fn(Achievement $achievement): array => $achievementPreviewer->preview($achievement),
            $achievements
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
