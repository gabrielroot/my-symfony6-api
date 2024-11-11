<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\PropertyAccess\Exception\InvalidArgumentException;

class ExceptionListener
{
    public function __invoke(ExceptionEvent $event): void
    {
        // You get the exception object from the received event
        $exception = $event->getThrowable();
        $message = 'Ocorreu um erro interno.';

        // Customize your response object to display the exception details
        $response = new JsonResponse();
        // the exception message can contain unfiltered user input;
        // set the content-type to text to avoid XSS issues
        $response->headers->set('Content-Type', 'application/json; charset=utf-8');

        // HttpExceptionInterface is a special type of exception that
        // holds status code and header details
        if ($exception instanceof HttpExceptionInterface) {
            $response->setStatusCode($exception->getStatusCode());
            $response->headers->replace($exception->getHeaders());
        } elseif ($exception instanceof InvalidArgumentException) {
            $message = "Requisição mal formada, favor rever os parâmetros e tentar novamente.";
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
        } else {
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $response->setContent(json_encode([
            'message' => $message,
            'internalMessage' => $exception->getMessage(),
            'success' => false]));

        // sends the modified response object to the event
        $event->setResponse($response);
    }
}