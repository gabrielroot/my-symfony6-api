<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as BaseController;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractController extends BaseController
{
    public function jsonResponse(
        $data,
        string $message = "",
        string $internalMessage = "",
        bool $success = true,
        int $statusCode = Response::HTTP_OK)
    {
        $responseData = [];
        if ($internalMessage) {
            $responseData[] =  ['internalMessage' => $internalMessage];
        }

        if ($message) {
            $responseData[] = ['message' => $message];
        }

        $responseData = array_merge($data, $responseData, ['success' => $success]);

        return $this->json($responseData, $statusCode);
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