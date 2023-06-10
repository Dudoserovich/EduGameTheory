<?php

namespace App\Controller;

use App\Service\FileUploader;
use Exception;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Uploads")
 **/
#[Route('/uploads', name: 'uploads_')]
class UploadsController extends ApiController
{
    /**
     * Get all images by entityName
     * @OA\Response(
     *     response=200,
     *     description="HTTP_OK",
     *     @OA\JsonContent(
     *         type="array",
     *         @OA\Items(
     *            type="string"
     *         )
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
    #[Security(name: null)]
    #[Route('/{entityName}',
        name: 'get_images_by_entity_name',
        requirements: ['entityName' => '[A-z]+'],
        methods: ['GET'])
    ]
    public function getImagesByEntityName(
        string $entityName,
        FileUploader $fileUploader): BinaryFileResponse|JsonResponse
    {
        $parentDir = $this->getParameter('upload.directory');
        $childrenDir = $entityName;
        $files = scandir($parentDir . $childrenDir);
        $nameFiles = array_diff($files, array('.', '..'));

        if (!is_dir($parentDir . $childrenDir))
            return $this->respondNotFound("No entity");
        elseif (!$nameFiles)
            return $this->respondNotFound("No images");
        else return $this->response(
            array_values(
                array_map(function($file) use ($entityName, $fileUploader) {
                    $realImage = $fileUploader->load($entityName, $file);

                    $imageSize = getimagesize($realImage);
                    $imageData = base64_encode(file_get_contents($realImage));
                    $imageSrc = "data:{$imageSize['mime']};base64,{$imageData}";

                    return $imageSrc;
                }, $nameFiles)
            )
        );

    }

    /**
     * Get all name images by entityName
     * @OA\Response(
     *     response=200,
     *     description="HTTP_OK",
     *     @OA\JsonContent(
     *         type="array",
     *         @OA\Items(
     *            type="string"
     *         )
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
    #[Security(name: null)]
    #[Route('/{entityName}/names',
        name: 'get_names_by_entity_name',
        requirements: ['entityName' => '[A-z]+'],
        methods: ['GET'])
    ]
    public function getNameImagesByEntityName(
        string $entityName,
        FileUploader $fileUploader): BinaryFileResponse|JsonResponse
    {
        $parentDir = $this->getParameter('upload.directory');
        $childrenDir = $entityName;
        $files = scandir($parentDir . $childrenDir);
        $nameFiles = array_diff($files, array('.', '..'));

        if (!is_dir($parentDir . $childrenDir))
            return $this->respondNotFound("No entity");
        elseif (!$nameFiles)
            return $this->respondNotFound("No images");
        else return $this->response(array_values($nameFiles));

    }

    /**
     * Get image entity
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
    #[Security(name: null)]
    #[Route('/{entityName}/{imageName}',
        name: 'get_image',
        requirements: ['entityName' => '[A-z]+'],
        methods: ['GET'])
    ]
    public function getImage(
        string $entityName,
        string $imageName,
        FileUploader $fileUploader): BinaryFileResponse|JsonResponse
    {
        try {
            $realImage = $fileUploader->load($entityName, $imageName);
        } catch (Exception $e) {
            return $this->respondValidationError("This image or entity not found!");
        }

//        $imageSize = getimagesize($realImage);
//        $imageData = base64_encode(file_get_contents($realImage));
//        $imageSrc = "data:{$imageSize['mime']};base64,{$imageData}";
//
//        return $this->response($imageSrc);
        return new BinaryFileResponse($realImage);
    }

}
