<?php


namespace App\Controller\user;

use App\Model\CreateUserRequest;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractController
{

    /**
     * @Route("/users", name="create_user", methods={"POST"})
     */
    public function createUser(
        Request $request,
        ValidatorInterface $validator,
        SerializerInterface $serializer,
        UserService $userService
    ): JsonResponse {
        try {
            $userRequest = $serializer->deserialize($request->getContent(), CreateUserRequest::class, 'json');

            $errors = $validator->validate($userRequest);
            if (count($errors) > 0) {
                $errorMessages = [];
                foreach ($errors as $error) {
                    $errorMessages[] = $error->getMessage();
                }
                return new JsonResponse(['errors' => $errorMessages], JsonResponse::HTTP_BAD_REQUEST);
            }

            $userService->saveUser($userRequest);

            return new JsonResponse(['status' => 'User created!'], JsonResponse::HTTP_CREATED);

        } catch (NotEncodableValueException $e) {
            return new JsonResponse(['error' => 'Invalid JSON'], JsonResponse::HTTP_BAD_REQUEST);
        }
    }
}