<?php

namespace App\Controller;

use App\Entity\User;
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

    public function respondValidationError($message = 'Data no valid'): JsonResponse
    {
        return $this->respondWithErrors($message, Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function respondNotFound($message = 'Not found!'): JsonResponse
    {
        return $this->respondWithErrors($message, Response::HTTP_NOT_FOUND);
    }

    protected function getUserEntity(): User
    {
        return $this->getDoctrine()->getRepository(User::class)
            ->findOneBy(['login' => parent::getUser()->getUserIdentifier()]);
    }

    protected function setSoftDeleteable(bool $enabled = true): void
    {
        $set = $enabled
            ? fn(string $filter) => $this->getDoctrine()->getManager()->getFilters()->enable($filter)
            : fn(string $filter) => $this->getDoctrine()->getManager()->getFilters()->disable($filter);
        $set("softdeleteable");
    }
}