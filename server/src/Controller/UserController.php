<?php

namespace App\Controller;

use ApiPlatform\Validator\ValidatorInterface;
use App\Entity\User;

use App\Previewer\UserPreviewer;
use App\Repository\UserRepository;

use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

//use ApiPlatform\Core\Validator\ValidatorInterface;

use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;

# TODO: Разобраться с soft delete
# TODO: Запросы на получение пользователя с его достижениями
# TODO: Запросы на получение пользователя с его процессом обучения
# TODO: Запросы на получение пользователя с его пройденными заданиями вне обучения

/**
 * @OA\Tag(name="User")
 * @Security(name="Bearer")
 */
#[Route('/users', name: 'users_')]
class UserController extends ApiController
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
    #[Route('/avatar/self',
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
     * Get all users except the authorized user
     * @OA\Response(
     *     response=200,
     *     description="HTTP_OK",
     *     @OA\JsonContent(
     *         type="array",
     *         @OA\Items(ref="#/components/schemas/UserView")
     *     )
     * )
     * @OA\Response(
     *     response=403,
     *     description="Permission denied"
     * )
     */
    #[Route(name: 'get', methods: ['GET'])]
    public function getUsers(UserPreviewer $userPreviewer): JsonResponse
    {
        $users = $this->userRepository->findNotUser($this->getUserEntity($this->userRepository)->getId());
//        $this->setSoftDeleteable($this->em, false);

        $userPreviews = array_map(
            fn(User $user): array => $userPreviewer->preview($user),
            $users
        );

        return $this->response($userPreviews);
    }

    /**
     * Add new user
     * @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *         example={"login": "pupil",
     *                  "password": "pupil123",
     *                  "email": "pupil@mail.ru",
     *                  "fio": "Иваненко Иван Иванович",
     *                  "roles": "ROLE_USER",
     *                  "avatar": "serious_cat.png"},
     *         @OA\Property(property="login", ref="#/components/schemas/User/properties/login"),
     *         @OA\Property(property="password", ref="#/components/schemas/User/properties/password"),
     *         @OA\Property(property="email", nullable=true, ref="#/components/schemas/User/properties/email"),
     *         @OA\Property(property="fio", nullable=true, ref="#/components/schemas/User/properties/fio"),
     *         @OA\Property(property="roles", nullable=true, ref="#/components/schemas/UserView/properties/roles"),
     *         @OA\Property(property="avatar", nullable=true, ref="#/components/schemas/UserView/properties/avatar"),
     *     )
     * )
     * @OA\Response(
     *     response=200,
     *     description="User added successgully"
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
    public function postUser(Request                     $request,
                             UserPasswordHasherInterface $passwordEncoder,
                             ValidatorInterface          $validator): JsonResponse
    {
        $request = $request->request->all();

        try {
            $this->setSoftDeleteable($this->em, false);
            $user = $this->userRepository->findOneBy(['login' => $request['login']]);
            if ($user) {
                if ($user->getDeletedAt()) {
                    $user->setDeletedAt(null);
                    $this->em->persist($user);
                    $this->em->flush();
                    $this->setSoftDeleteable($this->em);
                    return $this->respondWithSuccess("User added successfully");
                }

                return $this->respondValidationError('User with this login is already exist');
            }
            $user = new User();

            $user->setUsername($request['login']);
            $user->setPassword($request['password']);

            if (isset($request['fio'])) {
                $user->setFio($request['fio']);
            }
            if (isset($request['roles'])) {
                $user->setRoles([$request['roles']]);
            }
            if (isset($request['email'])) {
                $user->setEmail($request['email']);
            }
            if (isset($request['avatar'])) {
                $files = scandir($this->avatarDirectory);
                $files = array_diff($files, array('.', '..'));

                if (!in_array($request['avatar'], $files))
                    return $this->respondNotFound("Avatar not found");

                $user->setAvatar($request['avatar']);
            }

            $validator->validate($user);

            $user->setPassword(
                $passwordEncoder->hashPassword(
                    $user,
                    $request['password']
                )
            );

            $this->em->persist($user);
            $this->em->flush();

            return $this->respondWithSuccess("User added successfully");
        } catch (Exception) {
            return $this->respondValidationError();
        }
    }

    /**
     * User object
     * @OA\Response(
     *     response=200,
     *     description="HTTP_OK",
     *     @OA\JsonContent(ref="#/components/schemas/UserView")
     * )
     * @OA\Response(
     *     response=403,
     *     description="Permission denied!"
     * )
     * @OA\Response(
     *     response=404,
     *     description="User not found"
     * )
     */
    #[Route('/{userId}',
        name: 'get_by_id',
        requirements: ['userId' => '\d+'],
        methods: ['GET']
    )]
    public function getUserObj(
        UserPreviewer $userPreviewer,
        int $userId
    ): JsonResponse
    {
        $user = $this->userRepository->find($userId);
        if (!$user) {
            return $this->respondNotFound("User not found");
        }

//        $this->setSoftDeleteable($this->em, false);

        return $this->response($userPreviewer->preview($user));
    }

    /**
     * Change fields for user
     * @OA\RequestBody (
     *     required=true,
     *     @OA\JsonContent(
     *         @OA\Property(property="login", nullable=true, ref="#/components/schemas/UserView/properties/login"),
     *         @OA\Property(property="password", nullable=true, ref="#/components/schemas/User/properties/password"),
     *         @OA\Property(property="fio", nullable=true, ref="#/components/schemas/UserView/properties/fio"),
     *         @OA\Property(property="email", nullable=true, ref="#/components/schemas/UserView/properties/email"),
     *         @OA\Property(property="roles", nullable=true, ref="#/components/schemas/UserView/properties/roles"),
     *         @OA\Property(property="avatar", nullable=true, ref="#/components/schemas/UserView/properties/avatar")
     *     )
     * )
     * @OA\Response(
     *     response=200,
     *     description="User updated successgully"
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
     *     description="User not found"
     * )
     */
    #[Route('/{userId}',
        name: 'put_by_id',
        requirements: ['userId' => '\d+'],
        methods: ['PUT']
    )]
    public function upUser(Request                     $request,
                           UserPasswordHasherInterface $passwordEncoder,
                           ValidatorInterface          $validator,
                           int                         $userId): JsonResponse
    {
        $user = $this->userRepository->find($userId);
        if (!$user) {
            return $this->respondNotFound("User not found");
        }

        $request = $request->request->all();

        try {
            if (isset($request['login'])) {
                $login = $request['login'];

                if ($this->userRepository->findOneBy(['login' => $request['login']])) {
                    return $this->respondValidationError('User with this login is already exist');
                }

                $user->setUsername($login);
            }
            if (isset($request['password'])) {
                $password = $request['password'];

                $user->setPassword($password);
            }
            if (isset($request['fio'])) {
                $user->setFio($request['fio']);
            }
            if (isset($request['roles'])) {
                $user->setRoles([$request['roles']]);
            }
            if (isset($request['email'])) {
                $user->setEmail($request['email']);
            }
            if (isset($request['avatar'])) {
                $files = scandir($this->avatarDirectory);
                $files = array_diff($files, array('.', '..'));

                if (!in_array($request['avatar'], $files))
                    return $this->respondNotFound("Avatar not found");

                $user->setAvatar($request['avatar']);
            }

            $validator->validate($user);

            if (isset($request['password'])) {
                $user->setPassword($passwordEncoder->hashPassword($user, $request['password']));
            }

            $this->em->flush();

            return $this->respondWithSuccess("User updated successfully");
        } catch (Exception) {
            return $this->respondValidationError();
        }
    }

    /**
     * Delete user
     * @OA\Response(
     *     response=200,
     *     description="User deleted successgully"
     * )
     * @OA\Response(
     *     response=403,
     *     description="Permission denied!"
     * )
     * @OA\Response(
     *     response=404,
     *     description="User not found"
     * )
     */
    #[Route('/{userId}',
        name: 'delete_by_id',
        requirements: ['userId' => '\d+'],
        methods: ['DELETE']
    )]
    public function delUser(int $userId): JsonResponse
    {
        $user = $this->userRepository->find($userId);
        if (!$user) {
            return $this->respondNotFound("User not found");
        }

        $this->em->remove($user);
        $this->em->flush();

        return $this->respondWithSuccess("User deleted successfully");
    }

    /**
     * Get info about user
     * @OA\Response(
     *     response=200,
     *     description="HTTP_OK",
     *     @OA\JsonContent(ref="#/components/schemas/UserView")
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
    #[Route('/self',
        name: 'get_info',
        methods: ['GET']
    )]
    public function getSelf(UserPreviewer $userPreviewer): JsonResponse
    {
//        $this->setSoftDeleteable($this->em, false);
        try {
            return $this->response($userPreviewer->preview($this->getUserEntity($this->userRepository)));
        } catch (Exception $e) {
            return $this->respondValidationError($e->getMessage());
        }
    }

    /**
     * Change fields for user
     * @OA\RequestBody (
     *     required = true,
     *     @OA\JsonContent(
     *         example={
     *                  "login": "pupil",
     *                  "oldPassword": "pupil123",
     *                  "newPassword": "qwerty123",
     *                  "email": "pupil@mail.ru",
     *                  "fio": "Иваненко Иван Иванович",
     *                  "avatar": "serious_cat.png"
     *          },
     *         @OA\Property(property="login", nullable=true, ref="#/components/schemas/User/properties/login"),
     *         @OA\Property(property="oldPassword", nullable=true, ref="#/components/schemas/User/properties/password"),
     *         @OA\Property(property="newPassword", nullable=true, ref="#/components/schemas/User/properties/password"),
     *         @OA\Property(property="fio", nullable=true, ref="#/components/schemas/User/properties/fio"),
     *         @OA\Property(property="email", nullable=true, ref="#/components/schemas/User/properties/email"),
     *         @OA\Property(property="avatar", nullable=true, ref="#/components/schemas/User/properties/avatar")
     *     )
     * )
     * @OA\Response(
     *     response=200,
     *     description="User updated successgully"
     * )
     * * @OA\Response(
     *     response=403,
     *     description="Permission denied!"
     * )
     * @OA\Response(
     *     response=422,
     *     description="Data no valid"
     * )
     */
    #[Route('/self', name: 'self_put', methods: ['PUT'])]
    public function upSelf(Request $request,
                           UserPasswordHasherInterface $passwordEncoder
    ): JsonResponse
    {
        $user = $this->getUserEntity($this->userRepository);
        $request = $request->request->all();
        try {
            if (isset($request['login'])) {
                $userRepository = $this->em->getRepository(User::class);
                $userExist = (bool)$userRepository->findOneBy(['login' => $request['login']]);
                if ($userExist && $user->getUserIdentifier() != $request['login']) {
                    return $this->respondValidationError('User with this login is already exist');
                }
                $user->setUsername($request['login']);
            }
            if (isset($request['fio'])) {
                $user->setFio($request['fio']);
            }
            if (isset($request['email'])) {
                $email = $request['email'];
                if (!(filter_var($email, FILTER_VALIDATE_EMAIL))) {
                    return $this->respondValidationError("No valid email");
                }
                $user->setEmail($email);
            }
            if (isset($request['avatar'])) {
                $files = scandir($this->avatarDirectory);
                $files = array_diff($files, array('.', '..'));

                if (!in_array($request['avatar'], $files))
                    return $this->respondNotFound("Avatar not found");

                $user->setAvatar($request['avatar']);
            }

            if (!isset($request['oldPassword']) && isset($request['newPassword'])) {
                if (!$user->getPassword()) {
                    $user->setPassword($passwordEncoder->hashPassword($user, $request['newPassword']));
                } else {
                    return $this->respondValidationError("Old password is not set");
                }
            } else if (isset($request['oldPassword']) && !isset($request['newPassword'])) {
                return $this->respondValidationError("New password is not set");
            } else if (isset($request['oldPassword']) && isset($request['newPassword'])) {
                $oldPassword = $request['oldPassword'];
                $newPassword = $request['newPassword'];

                if (!$passwordEncoder->isPasswordValid($user, $oldPassword)) {
                    return $this->respondValidationError("Old password is not right");
                } else {
                    $user->setPassword($passwordEncoder->hashPassword($user, $newPassword));
                }
            }

            $this->em->flush();

            return $this->respondWithSuccess("User updated successfully");
        } catch (Exception) {
            return $this->respondValidationError();
        }
    }
}