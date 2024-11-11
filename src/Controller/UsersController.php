<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/users', name: 'users_')]
class UsersController extends AbstractController
{
    #[Route('/', name: 'list', methods: ['GET'])]
    public function getUsersAction(
        SerializerInterface $serializer,
    )
    {
//        $data = $serializer->serialize(['test' => 1400], 'json');
        $data = new User();

        $data->setUsername('randomname');

        return $this->jsonResponse($data->toArray());
    }

    #[Route('/', name: 'create', methods: ['POST'])]
    public function postUsersAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user, ['csrf_protection' => false,]);
        $form->submit($request->request->all());

        if(!$form->isValid()) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Erro na validação do formulário.',
                'internalMessage' => $form->getErrors()->__toString(),
            ], Response::HTTP_BAD_REQUEST);
        }

        die(dump($user));

        return $this->jsonResponse([
            'success' => true,
            'message' => 'Usuário criado com sucesso!'
        ], Response::HTTP_CREATED);
    }
}