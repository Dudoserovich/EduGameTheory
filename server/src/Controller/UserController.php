<?php

namespace App\Controller;

use ApiPlatform\Validator\ValidatorInterface;
use App\Entity\User;

use App\Previewer\UserPreviewer;
use App\Repository\UserRepository;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
//use ApiPlatform\Core\Validator\ValidatorInterface;

use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;

#[Route('/users', name: 'users_')]
class UserController extends ApiController
{
    private UserRepository $userRepository;
    private EntityManagerInterface $em;

    public function __construct(UserRepository $userRepository, EntityManagerInterface $em)
    {
        $this->userRepository = $userRepository;
        $this->em = $em;
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
     * @OA\Tag(name="User")
     * @Security(name="Bearer")
     */
    #[Route(name: 'get', methods: ['GET'])]
    public function getUsers(UserPreviewer $userPreviewer): JsonResponse
    {
        $users = $this->userRepository->findNotUser($this->getUserEntity()->getId());
        $this->setSoftDeleteable(false);

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
     *         @OA\Property(property="login", ref="#/components/schemas/User/properties/login"),
     *         @OA\Property(property="password", ref="#/components/schemas/User/properties/password"),
     *         @OA\Property(property="email", nullable=true, ref="#/components/schemas/User/properties/email"),
     *         @OA\Property(property="fio", nullable=true, ref="#/components/schemas/User/properties/fio"),
     *     )
     * )
     * @OA\Response(
     *     response=200,
     *     description="Category added successgully"
     * )
     * @OA\Response(
     *     response=403,
     *     description="Permission denied!"
     * )
     * @OA\Response(
     *     response=422,
     *     description="Data no valid"
     * )
     * @OA\Tag(name="User")
     * @Security(name="Bearer")
     */
    #[Route(name: 'post', methods: ['POST'])]
    public function postUser(Request                     $request,
                             UserPasswordHasherInterface $passwordEncoder,
                             ValidatorInterface          $validator): JsonResponse
    {
        $request = $request->request->all();
        $user = new User();

        try {
            if ($this->userRepository->findOneBy(['login' => $request['login']])) {
                return $this->respondValidationError('User with this login is already exist');
            }
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
     * Delete users
     * @OA\RequestBody(
     *     @OA\JsonContent(
     *         @OA\Property(
     *             property="user_id",
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/User/properties/id")
     *         )
     *     )
     * )
     * @OA\Response(
     *     response=200,
     *     description="Users deleted successfully"
     * )
     * @OA\Response(
     *     response=403,
     *     description="Permission denied!"
     * )
     * @OA\Response(
     *     response=404,
     *     description="User not found"
     * )
     * @OA\Tag(name="User")
     * @Security(name="Bearer")
     */
    #[Route(name: 'delete', methods: ['DELETE'])]
    public function delUsers(Request $request): JsonResponse
    {
        $request = $request->request->all();

        try {
            $userIds = $request['users_id'];

            $this->em->beginTransaction();
            foreach ($userIds as $userId) {
                $user = $this->userRepository->find($userId);
                if (!$user) {
                    $this->em->rollback();
                    return $this->respondNotFound("User not found");
                }
                $this->em->remove($user);
            }
            $this->em->flush();
            $this->em->commit();

            return $this->respondWithSuccess("Users deleted successfully");
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
     * @OA\Tag(name="User")
     * @Security(name="Bearer")
     */
    #[Route('/{userId}', name: 'get_by_id', requirements: ['userId' => '\d+'], methods: ['GET'])]
    public function getUserApi(UserPreviewer $userPreviewer, int $userId): JsonResponse
    {
        $user = $this->userRepository->find($userId);
        if (!$user) {
            return $this->respondNotFound("User not found");
        }

        $this->setSoftDeleteable(false);

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
     *         @OA\Property(property="roles", nullable=true, ref="#/components/schemas/UserView/properties/roles")
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
     * @OA\Tag(name="User")
     * @Security(name="Bearer")
     */
    #[Route('/{userId}', name: 'put_by_id', requirements: ['userId' => '\d+'], methods: ['PUT'])]
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
                if (!$login) {
                    throw new Exception();
                }
                if ($this->userRepository->findOneBy(['login' => $request['login']])) {
                    return $this->respondValidationError('User with this login is already exist');
                }

                $user->setUsername($login);
            }
            if (isset($request['password'])) {
                $password = $request['password'];
                if (!$password) {
                    throw new Exception();
                }
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
     * @OA\Tag(name="User")
     * @Security(name="Bearer")
     */
    #[Route('/{userId}', name: 'delete_by_id', requirements: ['userId' => '\d+'], methods: ['DELETE'])]
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
     * @OA\Tag(name="User")
     * @Security(name="Bearer")
     */
    #[Route('/self', name: 'get_info', methods: ['GET'])]
    public function getSelf(UserPreviewer $userPreviewer): JsonResponse
    {
        $this->setSoftDeleteable(false);
        return $this->response($userPreviewer->preview($this->getUserEntity()));
    }

    /**
     * Change fields for user
     * @OA\RequestBody (
     *     required = true,
     *     @OA\JsonContent(
     *         @OA\Property(property="login", nullable=true, ref="#/components/schemas/User/properties/login"),
     *         @OA\Property(property="oldPassword", nullable=true, ref="#/components/schemas/User/properties/password"),
     *         @OA\Property(property="newPassword", nullable=true, ref="#/components/schemas/User/properties/password"),
     *         @OA\Property(property="fio", nullable=true, ref="#/components/schemas/User/properties/fio"),
     *         @OA\Property(property="email", nullable=true, ref="#/components/schemas/User/properties/email")
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
     * @OA\Tag(name="User")
     * @Security(name="Bearer")
     */
    #[Route('/self', name: 'self_put', methods: ['PUT'])]
    public function upSelf(Request $request, UserPasswordHasherInterface $passwordEncoder): JsonResponse
    {
        $user = $this->getUserEntity();
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