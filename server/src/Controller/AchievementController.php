<?php

namespace App\Controller;

use App\Entity\Achievement;
use App\Previewer\AchievementPreviewer;
use App\Repository\AchievementRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Achievement")
 * @Security(name="Bearer")
 **/
#[Route('/achievements', name: 'achievements_')]
class AchievementController extends ApiController
{
    private AchievementRepository $achievementRepository;
    private EntityManagerInterface $em;

    public function __construct(AchievementRepository $achievementRepository, EntityManagerInterface $em)
    {
        $this->achievementRepository = $achievementRepository;
        $this->em = $em;
    }

    /**
     * Add new achievement
     * @OA\RequestBody(
     *     required=true,
     *     @OA\MediaType(
     *         mediaType="multipart/form-data",
     *         @OA\Schema(
     *             @OA\Property(
     *                  property="name",
     *                  ref="#/components/schemas/AchievementView/properties/name"
     *             ),
     *             @OA\Property(
     *                  property="description",
     *                  ref="#/components/schemas/AchievementView/properties/description"
     *             ),
     *             @OA\Property(
     *                  property="imageFile",
     *                  nullable=false,
     *                  ref="#/components/schemas/AchievementView/properties/imageFile"
     *             ),
     *         )
     *     )
     * )
     * @OA\Response(
     *     response=200,
     *     description="Achievement added successfully"
     * )
     * @OA\Response(
     *     response=403,
     *     description="Permission denied!"
     * )
     * @OA\Response(
     *     response=422,
     *     description="Data no valid"
     * )
     */
    #[Route(name: 'post', methods: ['POST'])]
    public function postAchievement(
        Request $request,
        FileUploader $fileUploader
    ): JsonResponse
    {
        $jsonRequest = $request->request->all();

        $imageFile = $request->files->get('imageFile');
        if (!$imageFile) {
            return $this->respondValidationError("File for achievement not transferred");
        }

        $achievementByName = $this->achievementRepository
            ->findOneBy(["name" => $jsonRequest['name']]);

        if (!$achievementByName) {
            try {
                $achievement = new Achievement();
                $movedImageFile = $fileUploader->upload($imageFile);

                $achievement
                    ->setName($jsonRequest['name'])
                    ->setDescription($jsonRequest['description'])
                    ->setImageSize($movedImageFile->getSize())
                    ->setImageName($movedImageFile->getFilename())
                    ->setImageFile($movedImageFile);

                $this->em->persist($achievement);
                $this->em->flush();

                return $this->respondWithSuccess("Achievement added successfully");
            } catch (Exception) {
                return $this->respondValidationError();
            }
        } else {
            return $this->respondValidationError("Achievement with this name is already exists");
        }
    }

    /**
     * Get image achievement
     * @OA\Response(
     *     response=200,
     *     description="HTTP_OK",
     *     @OA\MediaType(
     *          mediaType="images/*",
     *          @OA\Schema(ref="#/components/schemas/AchievementView/properties/imageFile")
     *     )
     * )
     * @OA\Response(
     *     response=403,
     *     description="Permission denied!"
     * )
     * @OA\Response(
     *     response=404,
     *     description="Image not found"
     * )
     */
    #[Route('/{achievementId}/image',
        name: 'get_image',
        requirements: ['achievementId' => '\d+'],
        methods: ['GET'])
    ]
    public function getImage(
        int $achievementId,
        FileUploader $fileUploader): BinaryFileResponse|JsonResponse
    {
        $image = $this->achievementRepository->find($achievementId);
        if (!$image) {
            return $this->respondNotFound("Image not found");
        }

        $realImage = $fileUploader->load($image->getImageName());

        return new BinaryFileResponse($realImage);
    }

    /**
     * Get achievement object without image file
     * @OA\Response(
     *     response=200,
     *     description="HTTP_OK",
     *     @OA\JsonContent(
     *         @OA\Property(property="name", ref="#/components/schemas/AchievementView/properties/name"),
     *         @OA\Property(property="description", ref="#/components/schemas/AchievementView/properties/description"),
     *         @OA\Property(property="imageName", ref="#/components/schemas/Achievement/properties/imageName"),
     *         @OA\Property(property="imageSize", ref="#/components/schemas/Achievement/properties/imageSize")
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

    /**
     * Change name and description for achievement
     * @OA\RequestBody (
     *     required=true,
     *     @OA\JsonContent(
     *         @OA\Property(
     *              property="name",
     *              ref="#/components/schemas/AchievementView/properties/name"
     *         ),
     *         @OA\Property(
     *              property="description",
     *              ref="#/components/schemas/AchievementView/properties/description"
     *         )
     *     )
     * )
     * @OA\Response(
     *     response=200,
     *     description="Achievement updated successgully"
     * )
     * * @OA\Response(
     *     response=403,
     *     description="Permission denied!"
     * )
     * @OA\Response(
     *     response=422,
     *     description="Data no valid"
     * )
     *
     * @OA\Response(
     *     response=404,
     *     description="Achievement not found"
     * )
     */
    #[Route('/{achievementId}',
        name: 'put_by_id',
        requirements: ['achievementId' => '\d+'],
        methods: ['PUT']
    )]
    public function upAchievement(Request $request,
                                      int $achievementId,
                                      FileUploader $fileUploader): JsonResponse
    {
        $achievement = $this->achievementRepository
            ->find($achievementId);

        if (!$achievement) {
            return $this->respondNotFound("Achievement not found");
        }

        $jsonRequest = $request->request->all();

        try {
            if (isset($jsonRequest['name'])) {
                if ($this->achievementRepository->findOneBy(['name' => $jsonRequest['name']])) {
                    return $this->respondValidationError('Achievement with this name is already exist');
                }

                $achievement->setName($jsonRequest['name']);
            }

            if (isset($jsonRequest['description'])) {
                $achievement->setDescription($jsonRequest['description']);
            }

            $this->em->persist($achievement);
            $this->em->flush();

            return $this->respondWithSuccess("Achievement updated successfully");
        } catch (Exception) {
            return $this->respondValidationError();
        }
    }

    // TODO: изменение картинки достижения

    /**
     * Get all achievements except the authorized user
     * @OA\Response(
     *     response=200,
     *     description="HTTP_OK",
     *     @OA\JsonContent(
     *         type="array",
     *         @OA\Items(ref="#/components/schemas/AchievementView")
     *     )
     * )
     * @OA\Response(
     *     response=403,
     *     description="Permission denied"
     * )
     */
    #[Route(name: 'get', methods: ['GET'])]
    public function getAchievements(AchievementPreviewer $achievementPreviewer): JsonResponse
    {
        $achievements = $this->achievementRepository->findAll();

        $achievementPreviews = array_map(
            fn(Achievement $achievement): array => $achievementPreviewer->preview($achievement),
            $achievements
        );

        return $this->response($achievementPreviews);
    }

    /**
     * Delete achievement
     * @OA\Response(
     *     response=200,
     *     description="Achievement deleted successfully"
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
        name: 'delete_by_id',
        requirements: ['achievementId' => '\d+'],
        methods: ['DELETE']
    )]
    public function delAchievement(int $achievementId): JsonResponse
    {
        $achievement = $this->achievementRepository->find($achievementId);
        if (!$achievement) {
            return $this->respondNotFound("Achievement not found");
        }

        // TODO: удаление файла

        $this->em->remove($achievement);
        $this->em->flush();

        return $this->respondWithSuccess("Achievement deleted successfully");
    }
}
