<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Service\UserService;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Knp\Component\Pager\Exception\InvalidValueException;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/users', name: 'users_')]
class UsersController extends MyAbstractFOSRestController
{
    #[Route('/', name: 'list', methods: ['GET'])]
    public function getUsersAction(
        Request $request,
        UserService $userService,
        PaginatorInterface $paginator): Response
    {
        try {
            $pagination = $paginator->paginate(
                $userService->queryAll(),
                $request->query->getInt('page', 1),
                $this->pageLimit);
        } catch (InvalidValueException $exception) {
            return $this->jsonResponse(
                data: [],
                message: 'Erro na paginação: A entidade parece não possuir o campo que você deseja ordenar.',
                internalMessage: $exception->getMessage(),
                success: false,
                statusCode: Response::HTTP_BAD_REQUEST);
        }

        return $this->jsonResponse($pagination);
    }

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

            return $this->jsonResponse(data: $user, message: Response::HTTP_CREATED);
        }

        return $this->jsonResponse(
            data: $this->handleFormErrors($form),
            message: 'Requisição mal formada.',
            success: false,
            statusCode: Response::HTTP_BAD_REQUEST);
    }

    #[Route('/{uuid}', name: 'show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->jsonResponse($user);
    }

    #[Route('/{uuid}', name: 'update', methods: ['PUT'])]
    public function update(Request $request, UserService $userService, User $user): Response
    {
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

            return $this->jsonResponse($user);
        }

        return $this->jsonResponse(
            data: $this->handleFormErrors($form),
            message: 'Requisição mal formada.',
            success: false,
            statusCode: Response::HTTP_BAD_REQUEST);
    }

    #[Route('/{uuid}', name: 'delete', methods: ['DELETE'])]
    public function delete(UserService $userService, User $user): Response
    {
        $userService->deleteNow($user);
        return $this->jsonResponse(data: $user);
    }
}