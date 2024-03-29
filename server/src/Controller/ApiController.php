<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApiController extends AbstractController
{
    public function response($data, $statusCode = Response::HTTP_OK, $headers = []): JsonResponse
    {
        return $this->json(
            $data,
            $statusCode,
            $headers
        );
    }

    public function respondWithErrors($errors, $statusCode = Response::HTTP_OK, $headers = []): JsonResponse
    {
        return $this->json(
            [
                'status' => $statusCode,
                'errors' => $errors,
            ],
            $statusCode,
            $headers
        );
    }

    public function respondWithSuccess($success, $statusCode = Response::HTTP_OK, $headers = []): JsonResponse
    {
        return $this->json(
            [
                'status' => $statusCode,
                'success' => $success,
            ],
            $statusCode,
            $headers
        );
    }

    public function respondValidationError($message = 'Некорректные данные'): JsonResponse
    {
        return $this->respondWithErrors($message, Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function respondNotFound($message = 'Не найдено'): JsonResponse
    {
        return $this->respondWithErrors($message, Response::HTTP_NOT_FOUND);
    }

    protected function getUserEntity(UserRepository $userRepository): User
    {
        return $userRepository
            ->findOneBy(['id' => (int)parent::getUser()->getUserIdentifier()]);
    }

    protected function setSoftDeleteable(EntityManagerInterface $em, bool $enabled = true): void
    {
        $set = $enabled
            ? fn(string $filter) => $em->getFilters()->enable($filter)
            : fn(string $filter) => $em->getFilters()->disable($filter);
        $set("softdeleteable");
    }
}