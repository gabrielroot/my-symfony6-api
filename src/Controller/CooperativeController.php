<?php

namespace App\Controller;

use App\Entity\Cooperative;
use App\Form\CooperativeType;
use App\Service\CooperativeService;
use App\Utils\Enum\SerializerGroups;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Attribute\Model;

#[OA\Tag(name: 'Cooperatives')]
#[Route('/cooperatives', name: 'cooperatives_')]
class CooperativeController extends MyAbstractFOSRestController
{
    /**
     * Lists the active cooperatives.
     *
     * This call all the active cooperatives, paginated.
     */
    #[OA\Parameter(ref: '#/components/parameters/paginatorPage')]
    #[OA\Parameter(ref: '#/components/parameters/paginatorSort')]
    #[OA\Parameter(ref: '#/components/parameters/paginatorDirection')]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Returns the cooperatives.',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'success', type: 'boolean', example: true),
                new OA\Property(
                    property: 'data',
                    type: 'array',
                    items: new OA\Items(
                        ref: new Model(
                            type: Cooperative::class,
                            groups: [SerializerGroups::DEFAULT, SerializerGroups::AUDIT]
                        )
                    )
                ),
                new OA\Property(property: 'page', type: 'integer', example: 1),
                new OA\Property(property: 'lastPage', type: 'integer', example: 2),
                new OA\Property(property: 'hasNextPage', type: 'boolean', example: true),
                new OA\Property(property: 'hasPreviewsPage', type: 'boolean',example: false),
                new OA\Property(property: 'perPage', type: 'integer', example: 5),
                new OA\Property(property: 'totalItemCount', type: 'integer', example: 54),
                new OA\Property(property: 'sortDirection', type: 'string', example: 'ASC')
            ],
            type: 'object'

        )
    )]
    #[OA\Response(ref: '#/components/responses/badRequestResponse', response: Response::HTTP_BAD_REQUEST)]
    #[OA\Response(ref: '#/components/responses/internalErrorResponse', response: Response::HTTP_INTERNAL_SERVER_ERROR)]
    #[Route('/', name: 'list', methods: ['GET'])]
    public function index(
        Request $request,
        CooperativeService $cooperativeService,
        PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate(
            $cooperativeService->queryAll(),
            $request->query->getInt('page', 1),
            $this->pageLimit);

        return $this->jsonResponse($pagination);
    }

    /**
     * Create a new cooperative.
     *
     * Just creates a new and fresh cooperative.
     */
    #[OA\RequestBody(content: new OA\JsonContent(ref: new Model(type: CooperativeType::class)))]
    #[OA\Response(
        response: Response::HTTP_CREATED,
        description: 'Returns when the cooperative was successfully created.',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'success', type: 'boolean', example: true),
                new OA\Property(property: 'message', type: 'string', example: 'Cooperativa criada!'),
                new OA\Property(
                    property: 'data',
                    type: 'array',
                    items: new OA\Items(
                        ref: new Model(
                            type: Cooperative::class,
                            groups: [SerializerGroups::DEFAULT, SerializerGroups::AUDIT]
                        )
                    )
                )
            ],
            type: 'object'

        )
    )]
    #[OA\Response(ref: '#/components/responses/badRequestResponse', response: Response::HTTP_BAD_REQUEST)]
    #[OA\Response(ref: '#/components/responses/internalErrorResponse', response: Response::HTTP_INTERNAL_SERVER_ERROR)]
    #[Route('/', name: 'create', methods: ['POST'])]
    public function create(Request $request, CooperativeService $cooperativeService): Response
    {
        $cooperative = new Cooperative();
        $form = $this->createForm(CooperativeType::class, $cooperative);
        $form->submit($request->request->all());

        if($form->isValid()) {
            try {
                $cooperativeService->save($cooperative);
            } catch (UniqueConstraintViolationException) {
                return $this->jsonResponse(
                    data: $cooperative,
                    message: 'Já existe uma cooperativa com este nome.',
                    success: false,
                    statusCode: Response::HTTP_BAD_REQUEST);
            }

            return $this->jsonResponse(
                data: $cooperative,
                message: 'Usuário criado!',
                statusCode: Response::HTTP_CREATED);
        }

        return $this->jsonResponse(
            data: $this->handleFormErrors($form),
            message: 'Requisição mal formada.',
            success: false,
            statusCode: Response::HTTP_BAD_REQUEST);
    }

    /**
     * Search for a specific cooperative.
     *
     * Show a cooperative detailed.
     */
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Returns when the cooperative was found.',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'success', type: 'boolean', example: true),
                new OA\Property(
                    property: 'data',
                    type: 'array',
                    items: new OA\Items(
                        ref: new Model(
                            type: Cooperative::class,
                            groups: [SerializerGroups::DEFAULT, SerializerGroups::AUDIT]
                        )
                    )
                )
            ],
            type: 'object'

        )
    )]
    #[OA\Response(ref: '#/components/responses/notFoundResponse', response: Response::HTTP_NOT_FOUND)]
    #[OA\Response(ref: '#/components/responses/internalErrorResponse', response: Response::HTTP_INTERNAL_SERVER_ERROR)]
    #[Route('/{uuid}', name: 'show', methods: ['GET'])]
    public function show(Cooperative $cooperative): Response
    {
        return $this->jsonResponse(
            data: $cooperative,
            serializerGroups: [SerializerGroups::AUDIT, SerializerGroups::DEPTHS]);
    }

    /**
     * Update a specific cooperative.
     *
     * Look for an active cooperative and update it.
     */
    #[OA\RequestBody(content: new OA\JsonContent(ref: new Model(type: CooperativeType::class)))]
    #[OA\Response(ref: '#/components/responses/httpOkResponse', response: Response::HTTP_OK)]
    #[OA\Response(ref: '#/components/responses/badRequestResponse', response: Response::HTTP_BAD_REQUEST)]
    #[OA\Response(ref: '#/components/responses/notFoundResponse', response: Response::HTTP_NOT_FOUND)]
    #[OA\Response(ref: '#/components/responses/internalErrorResponse', response: Response::HTTP_INTERNAL_SERVER_ERROR)]
    #[Route('/{uuid}', name: 'update', methods: ['PUT'])]
    public function update(Request $request, CooperativeService $cooperativeService, Cooperative $cooperative): Response
    {
        $form = $this->createForm(CooperativeType::class, $cooperative);
        $form->submit($request->request->all());

        if($form->isValid()) {
            try {
                $cooperativeService->save($cooperative);
            } catch (UniqueConstraintViolationException) {
                return $this->jsonResponse(
                    data: $cooperative,
                    message: 'Este username já existe.',
                    success: false,
                    statusCode: Response::HTTP_BAD_REQUEST);
            }

            return $this->jsonResponse($cooperative);
        }

        return $this->jsonResponse(
            data: $this->handleFormErrors($form),
            message: 'Requisição mal formada.',
            success: false,
            statusCode: Response::HTTP_BAD_REQUEST);
    }

    /**
     * Delete a specific cooperative.
     *
     * Look for an active cooperative and delete it.
     */
    #[OA\Response(ref: '#/components/responses/httpOkResponse', response: Response::HTTP_OK)]
    #[OA\Response(ref: '#/components/responses/notFoundResponse', response: Response::HTTP_NOT_FOUND)]
    #[OA\Response(ref: '#/components/responses/internalErrorResponse', response: Response::HTTP_INTERNAL_SERVER_ERROR)]
    #[Route('/{uuid}', name: 'delete', methods: ['DELETE'])]
    public function delete(CooperativeService $cooperativeService, Cooperative $cooperative): Response
    {
        $cooperativeService->deleteNow($cooperative);
        return $this->jsonResponse(data: $cooperative);
    }
}