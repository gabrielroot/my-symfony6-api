<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Service\UserService;
use App\Utils\Enum\SerializerGroups;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Attribute\Model;

#[OA\Tag(name: 'Users')]
#[Route('/users', name: 'users_')]
class UsersController extends MyAbstractFOSRestController
{
    /**
     * Lists the active users.
     *
     * This call all the active users, paginated.
     */
    #[OA\Parameter(ref: '#/components/parameters/paginatorPage')]
    #[OA\Parameter(ref: '#/components/parameters/paginatorSort')]
    #[OA\Parameter(ref: '#/components/parameters/paginatorDirection')]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Returns the users.',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'success', type: 'boolean', example: true),
                new OA\Property(
                    property: 'data',
                    type: 'array',
                    items: new OA\Items(
                        ref: new Model(
                            type: User::class,
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
        UserService $userService,
        PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate(
            $userService->queryAll(),
            $request->query->getInt('page', 1),
            $this->pageLimit);

        return $this->jsonResponse($pagination);
    }

    /**
     * Create a new user.
     *
     * Just creates a new and fresh user.
     */
    #[OA\RequestBody(content: new OA\JsonContent(ref: new Model(type: UserType::class)))]
    #[OA\Response(
        response: Response::HTTP_CREATED,
        description: 'Returns when the user was successfully created.',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'success', type: 'boolean', example: true),
                new OA\Property(property: 'message', type: 'string', example: 'Usuário criado!'),
                new OA\Property(
                    property: 'data',
                    type: 'array',
                    items: new OA\Items(
                        ref: new Model(
                            type: User::class,
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
    public function create(Request $request, UserService $userService): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->submit($request->request->all());

        if($form->isValid()) {
            try {
                $userService->createUser($user);
            } catch (UniqueConstraintViolationException) {
                return $this->jsonResponse(
                    data: $user,
                    message: 'Este username já existe.',
                    success: false,
                    statusCode: Response::HTTP_BAD_REQUEST);
            }

            return $this->jsonResponse(
                data: $user,
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
     * Search for a specific user.
     *
     * Show a user detailed.
     */
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Returns when the user was found.',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'success', type: 'boolean', example: true),
                new OA\Property(
                    property: 'data',
                    type: 'array',
                    items: new OA\Items(
                        ref: new Model(
                            type: User::class,
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
    public function show(User $user): Response
    {
        return $this->jsonResponse($user);
    }

    /**
     * Update a specific user.
     *
     * Look for an active user and update it.
     */
    #[OA\RequestBody(content: new OA\JsonContent(ref: new Model(type: UserType::class)))]
    #[OA\Response(ref: '#/components/responses/httpOkResponse', response: Response::HTTP_OK)]
    #[OA\Response(ref: '#/components/responses/badRequestResponse', response: Response::HTTP_BAD_REQUEST)]
    #[OA\Response(ref: '#/components/responses/notFoundResponse', response: Response::HTTP_NOT_FOUND)]
    #[OA\Response(ref: '#/components/responses/internalErrorResponse', response: Response::HTTP_INTERNAL_SERVER_ERROR)]
    #[Route('/{uuid}', name: 'update', methods: ['PUT'])]
    public function update(Request $request, UserService $userService, User $user): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->submit($request->request->all());

        if($form->isValid()) {
            try {
                $userService->updateUser($user);
            } catch (UniqueConstraintViolationException) {
                return $this->jsonResponse(
                    data: $user,
                    message: 'Este username já existe.',
                    success: false,
                    statusCode: Response::HTTP_BAD_REQUEST);
            }

            return $this->jsonResponse($user);
        }

        return $this->jsonResponse(
            data: $this->handleFormErrors($form),
            message: 'Requisição mal formada.',
            success: false,
            statusCode: Response::HTTP_BAD_REQUEST);
    }

    /**
     * Delete a specific user.
     *
     * Look for an active user and delete it.
     */
    #[OA\Response(ref: '#/components/responses/httpOkResponse', response: Response::HTTP_OK)]
    #[OA\Response(ref: '#/components/responses/notFoundResponse', response: Response::HTTP_NOT_FOUND)]
    #[OA\Response(ref: '#/components/responses/internalErrorResponse', response: Response::HTTP_INTERNAL_SERVER_ERROR)]
    #[Route('/{uuid}', name: 'delete', methods: ['DELETE'])]
    public function delete(UserService $userService, User $user): Response
    {
        $userService->deleteNow($user);
        return $this->jsonResponse(data: $user);
    }
}