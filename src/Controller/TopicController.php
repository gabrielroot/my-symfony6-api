<?php

namespace App\Controller;

use App\Entity\Topic;
use App\Exception\BusinessRule\MissingTopicEndTimeException;
use App\Form\TopicType;
use App\Service\TopicService;
use App\Service\VoteService;
use App\Utils\Enum\SerializerGroups;
use Knp\Component\Pager\PaginatorInterface;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Topics')]
#[Route('/topics', name: 'topics_')]
class TopicController extends MyAbstractFOSRestController
{
    /**
     * Lists the active topics.
     *
     * This call all the active topics, paginated.
     */
    #[OA\Parameter(ref: '#/components/parameters/paginatorPage')]
    #[OA\Parameter(ref: '#/components/parameters/paginatorSort')]
    #[OA\Parameter(ref: '#/components/parameters/paginatorDirection')]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Returns the topics.',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'success', type: 'boolean', example: true),
                new OA\Property(
                    property: 'data',
                    type: 'array',
                    items: new OA\Items(
                        ref: new Model(
                            type: Topic::class,
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
        TopicService $topicService,
        PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate(
            $topicService->queryAll(),
            $request->query->getInt('page', 1),
            $this->pageLimit);

        return $this->jsonResponse($pagination);
    }

    /**
     * Create a new topic.
     *
     * Just creates a new and fresh topic.
     */
    #[OA\RequestBody(content: new OA\JsonContent(ref: new Model(type: TopicType::class)))]
    #[OA\Response(
        response: Response::HTTP_CREATED,
        description: 'Returns when the topic was successfully created.',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'success', type: 'boolean', example: true),
                new OA\Property(property: 'message', type: 'string', example: 'Cooperativa criada!'),
                new OA\Property(
                    property: 'data',
                    type: 'array',
                    items: new OA\Items(
                        ref: new Model(
                            type: Topic::class,
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
    public function create(Request $request, TopicService $topicService): Response
    {
        $topic = new Topic();
        $form = $this->createForm(TopicType::class, $topic);
        $form->submit($request->request->all());

        if($form->isValid()) {
            $topicService->createTopic($topic);

            return $this->jsonResponse(
                data: $topic,
                message: 'Tópico criado!',
                statusCode: Response::HTTP_CREATED);
        }

        return $this->jsonResponse(
            data: $this->handleFormErrors($form),
            message: 'Requisição mal formada.',
            success: false,
            statusCode: Response::HTTP_BAD_REQUEST);
    }

    /**
     * Search for a specific topic.
     *
     * Show a topic detailed.
     */
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Returns when the topic was found.',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'success', type: 'boolean', example: true),
                new OA\Property(
                    property: 'data',
                    type: 'array',
                    items: new OA\Items(
                        ref: new Model(
                            type: Topic::class,
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
    public function show(Topic $topic): Response
    {
        return $this->jsonResponse(
            data: $topic,
            serializerGroups: [SerializerGroups::AUDIT, SerializerGroups::DEPTHS]);
    }

    /**
     * Search for a specific topic.
     *
     * Show a topic detailed.
     */
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Returns when the topic was found.',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'success', type: 'boolean', example: true),
                new OA\Property(
                    property: 'data',
                    type: 'array',
                    items: new OA\Items(
                        ref: new Model(
                            type: Topic::class,
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
    #[Route('/{uuid}/see-votes', name: 'see_votes', methods: ['GET'])]
    public function seeVotes(Topic $topic, VoteService $voteService): Response
    {

        return $this->jsonResponse(
            data: $voteService->countVotes($topic),
            serializerGroups: [SerializerGroups::AUDIT, SerializerGroups::DEPTHS]);
    }

    /**
     * Update a specific topic.
     *
     * Look for an active topic and update it.
     */
    #[OA\RequestBody(content: new OA\JsonContent(ref: new Model(type: TopicType::class)))]
    #[OA\Response(ref: '#/components/responses/httpOkResponse', response: Response::HTTP_OK)]
    #[OA\Response(ref: '#/components/responses/badRequestResponse', response: Response::HTTP_BAD_REQUEST)]
    #[OA\Response(ref: '#/components/responses/notFoundResponse', response: Response::HTTP_NOT_FOUND)]
    #[OA\Response(ref: '#/components/responses/internalErrorResponse', response: Response::HTTP_INTERNAL_SERVER_ERROR)]
    #[Route('/{uuid}', name: 'update', methods: ['PUT'])]
    public function update(Request $request, TopicService $topicService, Topic $topic): Response
    {
        $form = $this->createForm(TopicType::class, $topic);
        $form->submit($request->request->all());

        if($form->isValid()) {
            try {
                $topicService->updateTopic($topic);
            } catch (MissingTopicEndTimeException $exception) {
                return $this->jsonResponse(
                    data: $topic,
                    message: $exception->getMessage(),
                    success: false,
                    statusCode: Response::HTTP_BAD_REQUEST);
            }

            return $this->jsonResponse($topic);
        }

        return $this->jsonResponse(
            data: $this->handleFormErrors($form),
            message: 'Requisição mal formada.',
            success: false,
            statusCode: Response::HTTP_BAD_REQUEST);
    }

    /**
     * Delete a specific topic.
     *
     * Look for an active topic and delete it.
     */
    #[OA\Response(ref: '#/components/responses/httpOkResponse', response: Response::HTTP_OK)]
    #[OA\Response(ref: '#/components/responses/notFoundResponse', response: Response::HTTP_NOT_FOUND)]
    #[OA\Response(ref: '#/components/responses/internalErrorResponse', response: Response::HTTP_INTERNAL_SERVER_ERROR)]
    #[Route('/{uuid}', name: 'delete', methods: ['DELETE'])]
    public function delete(TopicService $topicService, Topic $topic): Response
    {
        $topicService->deleteNow($topic);
        return $this->jsonResponse(data: $topic);
    }
}