<?php

namespace App\Controller;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Avatar")
 */
#[Route('/avatars', name: 'avatars_')]
class AvatarController extends ApiController
{
    private UserRepository $userRepository;
    private EntityManagerInterface $em;
    private string $avatarDirectory;

    public function __construct(
        UserRepository $userRepository,
        EntityManagerInterface $em,
        string $avatarDirectory
    )
    {
        $this->userRepository = $userRepository;
        $this->em = $em;
        $this->avatarDirectory = $avatarDirectory;
    }

    /**
     * Get self avatar
     * @OA\Response(
     *     response=200,
     *     description="HTTP_OK",
     *     @OA\MediaType(
     *          mediaType="images/png",
     *          @OA\Schema(ref="#/components/schemas/AchievementView/properties/imageFile")
     *     )
     * )
     * @OA\Response(
     *     response=403,
     *     description="Permission denied!"
     * )
     * @OA\Response(
     *     response=404,
     *     description="Avatar not found"
     * )
     */
    #[Route('/self',
        name: 'get_self_avatar',
        methods: ['GET']
    )]
    public function getSelfAvatar(): BinaryFileResponse|JsonResponse
    {
        $user = $this->getUserEntity($this->userRepository);
        $selfAvatar = $user->getAvatar();

        $files = scandir($this->avatarDirectory);
        $files = array_diff($files, array('.', '..'));

        if (in_array($selfAvatar, $files)) {
            $file = new File($this->avatarDirectory . "/$selfAvatar");

            $imageSize = getimagesize($file);
            $imageData = base64_encode(file_get_contents($file));
            $imageSrc = "data:{$imageSize['mime']};base64,{$imageData}";

            return $this->response($imageSrc);
        } else {
            return $this->respondNotFound("Avatar not found");
        }

    }

    /**
     * Get specific avatar
     *
     * @OA\Parameter(
     *     name="avatar",
     *     in="path",
     *     description="avatar",
     *     required=true,
     *     example="angry_cat.png"
     * )
     *
     * @OA\Response(
     *     response=200,
     *     description="HTTP_OK",
     *     @OA\MediaType(
     *          mediaType="images/png",
     *          @OA\Schema(ref="#/components/schemas/AchievementView/properties/imageFile")
     *     )
     * )
     * @OA\Response(
     *     response=403,
     *     description="Permission denied!"
     * )
     * @OA\Response(
     *     response=404,
     *     description="Avatar not found"
     * )
     */
    #[Security(name: null)]
    #[Route('/{avatar}',
        name: 'get_spec_avatar',
        requirements: ['avatar' => '[A-z]+\.png'],
        methods: ['GET']
    )]
    public function getSpecificAvatar(string $avatar): BinaryFileResponse|JsonResponse
    {
        $files = scandir($this->avatarDirectory);
        $files = array_diff($files, array('.', '..'));

        if (in_array($avatar, $files)) {
            $file = new File($this->avatarDirectory . "/$avatar");

            return new BinaryFileResponse($file);
        } else {
            return $this->respondNotFound("Avatar not found");
        }

    }

    /**
     * Get all avatar names
     * @OA\Response(
     *     response=200,
     *     description="HTTP_OK"
     * )
     * @OA\Response(
     *     response=403,
     *     description="Permission denied!"
     * )
     * @OA\Response(
     *     response=404,
     *     description="Avatars not found"
     * )
     */
    #[Security(name: null)]
    #[Route('',
        name: 'get_all_avatars',
        methods: ['GET']
    )]
    public function getAllAvatars(): JsonResponse
    {
        $files = scandir($this->avatarDirectory);
        $files = array_diff($files, array('.', '..'));

        if (!$files)
            return $this->respondNotFound("No avatars");
        else return $this->response(array_values($files));

    }
}