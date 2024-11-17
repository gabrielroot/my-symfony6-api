<?php

namespace App\Controller;

use App\Utils\Enum\SerializerGroups;
use FOS\RestBundle\Context\Context;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;

abstract class MyAbstractFOSRestController extends AbstractFOSRestController
{
    public function jsonResponse(
        $data,
        string $message = "",
        string $internalMessage = "",
        bool $success = true,
        int $statusCode = Response::HTTP_OK,
        $serializerGroups = [SerializerGroups::DEFAULT, SerializerGroups::AUDIT])
    {
        $responseData = [];
        if ($internalMessage) {
            $responseData =  array_merge(['internalMessage' => $internalMessage]);
        }

        if ($message) {
            $responseData = array_merge(['message' => $message]);
        }

        if ($data) {
            $responseData = array_merge(['data' => $data], $responseData);
        }

        $responseData = array_merge(['success' => $success], $responseData);

        $view = $this->serializeObject(
            object: $responseData,
            statusCode: $statusCode,
            serializerGroups: $serializerGroups);

        return $this->handleView($view);
    }

    public function serializeObject(
        $object,
        int $statusCode = Response::HTTP_OK,
        array $serializerGroups = [SerializerGroups::DEFAULT, SerializerGroups::AUDIT]): View
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