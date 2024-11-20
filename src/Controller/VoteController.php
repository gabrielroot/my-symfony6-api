<?php

namespace App\Controller;

use App\Entity\Vote;
use App\Exception\MemberAlreadyVotedException;
use App\Exception\SessionClosedToVoteException;
use App\Exception\TopicNotFromMemberCooperativeException;
use App\Form\VoteType;
use App\Service\VoteService;
use App\Utils\Enum\SerializerGroups;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Attribute\Model;

#[OA\Tag(name: 'Votes')]
#[Route('/votes', name: 'votes_')]
class VoteController extends MyAbstractFOSRestController
{
    /**
     * Lists the active votes.
     *
     * This call all the active votes, paginated.
     */
    #[OA\Parameter(ref: '#/components/parameters/paginatorPage')]
    #[OA\Parameter(ref: '#/components/parameters/paginatorSort')]
    #[OA\Parameter(ref: '#/components/parameters/paginatorDirection')]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Returns the votes.',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'success', type: 'boolean', example: true),
                new OA\Property(
                    property: 'data',
                    type: 'array',
                    items: new OA\Items(
                        ref: new Model(
                            type: Vote::class,
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
        VoteService $voteService,
        PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate(
            $voteService->queryAll(),
            $request->query->getInt('page', 1),
            $this->pageLimit);

        return $this->jsonResponse($pagination);
    }

    /**
     * Create a new vote.
     *
     * Just creates a new and fresh vote.
     */
    #[OA\RequestBody(content: new OA\JsonContent(ref: new Model(type: VoteType::class)))]
    #[OA\Response(
        response: Response::HTTP_CREATED,
        description: 'Returns when the vote was successfully created.',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'success', type: 'boolean', example: true),
                new OA\Property(property: 'message', type: 'string', example: 'Voto enviado!'),
                new OA\Property(
                    property: 'data',
                    type: 'array',
                    items: new OA\Items(
                        ref: new Model(
                            type: Vote::class,
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
    public function create(Request $request, VoteService $voteService): Response
    {
        $vote = new Vote();
        $form = $this->createForm(VoteType::class, $vote);
        $form->submit($request->request->all());

        if($form->isValid()) {
            try {
                $voteService->createVote($vote);
            } catch (
                MemberAlreadyVotedException
                |SessionClosedToVoteException
                |TopicNotFromMemberCooperativeException $exception
            ) {
                return $this->jsonResponse(
                    data: $vote,
                    message: $exception->getMessage(),
                    success: false,
                    statusCode: Response::HTTP_BAD_REQUEST);
            }

            return $this->jsonResponse(
                data: $vote,
                message: 'Voto criado!',
                statusCode: Response::HTTP_CREATED);
        }

        return $this->jsonResponse(
            data: $this->handleFormErrors($form),
            message: 'Requisição mal formada.',
            success: false,
            statusCode: Response::HTTP_BAD_REQUEST);
    }

    /**
     * Search for a specific vote.
     *
     * Show a vote detailed.
     */
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Returns when the vote was found.',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'success', type: 'boolean', example: true),
                new OA\Property(
                    property: 'data',
                    type: 'array',
                    items: new OA\Items(
                        ref: new Model(
                            type: Vote::class,
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
    public function show(Vote $vote): Response
    {
        return $this->jsonResponse(
            data: $vote,
            serializerGroups: [SerializerGroups::AUDIT, SerializerGroups::DEPTHS]);
    }
}