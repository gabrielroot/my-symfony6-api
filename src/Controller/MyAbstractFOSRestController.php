<?php

namespace App\Controller;

use App\Repository\BaseRepository;
use App\Utils\Enum\SerializerGroups;
use FOS\RestBundle\Context\Context;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;

abstract class MyAbstractFOSRestController extends AbstractFOSRestController
{
    protected int $pageLimit = 5;

    public function jsonResponse(
        $data,
        string $message = "",
        string $internalMessage = "",
        bool $success = true,
        int $statusCode = Response::HTTP_OK,
        array $serializerGroups = [SerializerGroups::AUDIT]): Response
    {
        $responseData = ['success' => $success];
        $responseData = ($internalMessage) ?  array_merge(['internalMessage' => $internalMessage]) : $responseData;
        $responseData = ($message) ?  array_merge(['message' => $message], $responseData) : $responseData;

        if ($data instanceof SlidingPagination) {
            $responseData = array_merge($responseData, [
                'data' => $data->getItems(),
                'page' => $data->getCurrentPageNumber(),
                'lastPage' => $data->getPageCount(),
                'hasNextPage' => $data->getCurrentPageNumber() < $data->getPageCount(),
                'hasPreviewsPage' => $data->getCurrentPageNumber() > 1,
                'perPage' => $data->getItemNumberPerPage(),
                'totalItemCount' => $data->getTotalItemCount(),
                'sortDirection' => $data->getDirection()
            ]);
        } else {
            $responseData = array_merge($responseData, ['data' => $data]);
        }

        $view = $this->serializeObject(
            object: $responseData,
            statusCode: $statusCode,
            serializerGroups: array_merge([SerializerGroups::DEFAULT], $serializerGroups));

        return $this->handleView($view);
    }

    public function serializeObject(
        $object,
        int $statusCode = Response::HTTP_OK,
        array $serializerGroups = []): View
    {
        $context = new Context();
        $context
            ->enableMaxDepth()
            ->setGroups($serializerGroups)
            ->setSerializeNull(true);

        return $this->view($object, $statusCode)->setContext($context);
    }

    protected function handleFormErrors(FormInterface $form): array
    {
        $errors = [];

        foreach ($form->getErrors(true) as $error) {
            $origin = $error->getOrigin();
            $key = $origin->getName();

            while ($origin = $origin->getParent()) {
                $key = $origin->getName() . '_' . $key;
            }

            $errors[$key][] = $error->getMessage();
        }

        return $errors;
    }

//    public function jsonResponseByPaginator(
//        SlidingPagination|PaginatorInterface|Pagination $pagination,
//        int $statusCode = Response::HTTP_OK,
//        array $serializerGroups = [],
//        bool $translate = false,
//        bool $capitalize = false
//    ): JsonResponse
//    {
//        return $this->json(
//            array(
//                'success' => true,
//                'data' => $this->itemsToArray((array)$pagination->getItems(), $serializerGroups, $translate, $capitalize),
//                'page' => $pagination->getCurrentPageNumber(),
//                'lastPage' => $pagination->getPageCount(),
//                'nextPage' => $pagination->getCurrentPageNumber() < $pagination->getPageCount(),
//                'previewsPage' => $pagination->getCurrentPageNumber() > 1,
//                'perPage' => $pagination->getItemNumberPerPage(),
//                'totalItems' => $pagination->getTotalItemCount(),
//                'sort' => $pagination->getDirection()
//            ),
//            $statusCode
//        );
//    }
}