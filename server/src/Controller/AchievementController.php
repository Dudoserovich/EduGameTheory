<?php

namespace App\Controller;

use App\Entity\Achievement;
use App\Repository\AchievementRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;

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
     * @OA\Tag(name="Achievement")
     * @Security(name="Bearer")
     */
    #[Route('/upload', name: 'post', methods: ['POST'])]
    public function postAchievement(Request $request, FileUploader $fileUploader): JsonResponse
    {
        $jsonRequest = $request->request->all();

        $imageFile = $request->files->get('imageFile');
        if (!$imageFile) {
            return $this->respondValidationError("File for achievement not transferred");
        }

        $achievementByName = $this->achievementRepository
            ->findOneBy(["name" => $jsonRequest['name']]);
//        $achievementByImageName = $this->achievementRepository
//            ->findOneBy(["imageName" => $imageFile->getClientOriginalName()]);

//        if (!$achievementByName && !$achievementByImageName) {
        if (!$achievementByName) {
            try {
                $achievement = new Achievement();

                # TODO: записывать в имя название файла с солью из $fileUploader
                $achievement
                    ->setName($jsonRequest['name'])
                    ->setDescription($jsonRequest['description'])
                    ->setImageSize($imageFile->getSize())
                    ->setImageName($imageFile->getClientOriginalName())
                    ->setImageFile($imageFile);

                $fileUploader->upload($imageFile);

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

    #[Route('/{achievementId}', name: 'put_by_id', requirements: ['achievementId' => '\d+'], methods: ['PUT'])]
    public function uploadAchievement(Request $request,
                                      int $achievementId,
                                      FileUploader $fileUploader): void
    {
        # pass
    }
}
