<?php

namespace App\Controller;

use App\Entity\Term;
use App\Repository\TermRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Previewer\TermPreviewer;

use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;

#[Route('/terms', name: 'terms_')]
class TermController extends ApiController
{
    private TermRepository $termRepository;
    private EntityManagerInterface $em;

    public function __construct(TermRepository $termRepository, EntityManagerInterface $em)
    {
        $this->termRepository = $termRepository;
        $this->em = $em;
    }

    /**
     * Get all terms ordered
     * @OA\Response(
     *     response=200,
     *     description="HTTP_OK",
     *     @OA\JsonContent(
     *         type="array",
     *         @OA\Items(ref="#/components/schemas/TermView")
     *     )
     * )
     * @OA\Response(
     *     response=403,
     *     description="Permission deinied"
     * )
     * @OA\Tag(name="Term")
     * @Security(name="Bearer")
     */
//    #[Get(deprecated: true)]
    #[Route(name: 'get', methods: ['GET'])]
    public function getTerms(TermPreviewer $termPreviewer): JsonResponse
    {
        $termPreviewers = array_map(
            fn(Term $term): array => $termPreviewer->preview($term),
            $this->termRepository->findBy([], ["name" => "ASC"])
        );

        return $this->response($termPreviewers);
    }

    /**
     * Add new term
     * @OA\RequestBody (
     *     required=true,
     *     @OA\JsonContent(
     *         @OA\Property(property="name", ref="#/components/schemas/Term/properties/name"),
     *         @OA\Property(property="lvl", ref="#/components/schemas/Term/properties/description")
     *     )
     * )
     * @OA\Response(
     *     response=200,
     *     description="Term added successfully"
     * )
     * * @OA\Response(
     *     response=403,
     *     description="Permission denied!"
     * )
     * * @OA\Response(
     *     response=422,
     *     description="Data no valid"
     * )
     * @OA\Tag(name="Term")
     * @Security(name="Bearer")
     */
    #[Route(name: 'post', methods: ['POST'])]
    public function postTerm(Request $request): JsonResponse
    {
        $request = $request->request->all();

//        $this->setSoftDeleteable(false);
        $term = $this->termRepository->findOneBy(['name' => $request['name']]);
        if ($term)
            return $this->respondValidationError('A term with such name has already been created');

        $term = new Term();
        try {
            $term
                ->setName($request['name'])
                ->setDescription($request['description']);
            $this->em->persist($term);
            $this->em->flush();
            return $this->respondWithSuccess("Term added successfully");
        } catch (Exception) {
            return $this->respondValidationError();
        }
    }

    /**
     * Delete terms
     * @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *         @OA\Property(
     *             property="terms_id",
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Term/properties/id")
     *         )
     *     )
     * )
     * @OA\Response(
     *     response=200,
     *     description="Terms deleted successfully"
     * )
     * @OA\Response(
     *     response=403,
     *     description="Permission denied!"
     * )
     * @OA\Response(
     *     response=404,
     *     description="Term not found"
     * )
     * @OA\Tag(name="Term")
     * @Security(name="Bearer")
     */
    #[Route(name: 'delete', methods: ['DELETE'])]
    public function delTerms(Request $request): JsonResponse
    {
        $request = $request->request->all();

        try {
            $termsIds = $request['terms_id'];

            $this->em->beginTransaction();
            foreach ($termsIds as $termsId) {
                $term = $this->termRepository->find($termsId);
                if (!$term) {
                    $this->em->rollback();
                    return $this->respondNotFound("Term not found");
                }
                $this->em->remove($term);
            }
            $this->em->flush();
            $this->em->commit();

            return $this->respondWithSuccess("Terms deleted successfully");
        } catch (Exception) {
            return $this->respondValidationError();
        }
    }

    /**
     * Term object
     * @OA\Response(
     *     response=200,
     *     description="HTTP_OK",
     *     @OA\JsonContent(ref="#/components/schemas/TermView")
     * )
     * @OA\Response(
     *     response=403,
     *     description="Permission denied!"
     * )
     * @OA\Response(
     *     response=404,
     *     description="Term not found"
     * )
     * @OA\Tag(name="Term")
     * @Security(name="Bearer")
     */
    #[Route('/{termId}', name: 'get_by_id', requirements: ['termId' => '\d+'], methods: ['GET'])]
    public function getTerm(TermPreviewer $termPreviewer, int $termId): JsonResponse
    {
        $term = $this->termRepository->find($termId);
        if (!$term) {
            return $this->respondNotFound("Term not found");
        }

        return $this->response($termPreviewer->preview($term));
    }

    /**
     * Change field of term
     * @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *         @OA\Property(property="name", nullable=true, ref="#/components/schemas/Term/properties/name"),
     *         @OA\Property(property="lvl", nullable=true, ref="#/components/schemas/Term/properties/description")
     *     )
     * )
     * @OA\Response(
     *     response=200,
     *     description="Term updated successfully"
     * )
     * * @OA\Response(
     *     response=403,
     *     description="Permission denied!"
     * )
     * @OA\Response(
     *     response=404,
     *     description="Term not found"
     * )
     * @OA\Response(
     *     response=422,
     *     description="Data no valid"
     * )
     * @OA\Tag(name="Term")
     * @Security(name="Bearer")
     */
    #[Route('/{termId}', name: 'put_by_id', requirements: ['termId' => '\d+'], methods: ['PUT'])]
    public function upTerm(Request $request, int $termId): JsonResponse
    {
        $term = $this->termRepository->find($termId);
        if (!$term) {
            return $this->respondNotFound("Term not found");
        }

        $request = $request->request->all();

        try {
            if (isset($request['name'])) {
                $term->setName($request['name']);
            }
            if (isset($request['description'])) {
                $term->setDescription($request['description']);
            }

            $this->em->flush();

            return $this->respondWithSuccess("Term updated successfully");
        } catch (Exception) {
            return $this->respondValidationError();
        }
    }

    /**
     * Delete term
     * @OA\Response(
     *     response=200,
     *     description="Term deleted successfully"
     * )
     * @OA\Response(
     *     response=403,
     *     description="Permission denied!"
     * )
     * @OA\Response(
     *     response=404,
     *     description="Term not found"
     * )
     * @OA\Tag(name="Term")
     * @Security(name="Bearer")
     */
    #[Route('/{termId}', name: 'delete_by_id', requirements: ['termId' => '\d+'], methods: ['DELETE'])]
    public function delTerm(int $termId): JsonResponse
    {
        $term = $this->termRepository->find($termId);
        if (!$term) {
            return $this->respondNotFound("Term not found");
        }

        $this->em->remove($term);
        $this->em->flush();

        return $this->respondWithSuccess("Term deleted successfully");
    }
}
