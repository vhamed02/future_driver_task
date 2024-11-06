<?php

namespace App\State;

use ApiPlatform\Doctrine\Common\State\PersistProcessor;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private PersistProcessor       $persistProcessor,
    )
    {
        //
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        if ($operation instanceof Delete) {
            $userId = $uriVariables['id'];
            $user = $this->entityManager->getRepository(User::class)->find($userId);
            if (!$user) {
                return new JsonResponse(
                    [
                        'status' => 'error',
                        'message' => 'User not found.'
                    ],
                    JsonResponse::HTTP_NOT_FOUND
                );
            }
            $this->entityManager->remove($user);
            $this->entityManager->flush();
            return new JsonResponse([
                'status' => 'success',
                'message' => 'User with id ' . $userId . ' has been deleted.'
            ]);
        }

        if (!$data instanceof User) {
            throw new \InvalidArgumentException('Data must be an instance of User');
        }
        $data->setPassword(bin2hex(random_bytes(8)));

        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }
}
