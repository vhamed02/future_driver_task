<?php

namespace App\State;

use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class UserProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private Security $security
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
        if ($operation instanceof Post) {
            if (!$data instanceof User) {
                throw new \InvalidArgumentException('Data must be an instance of User');
            }
            $data->setPassword(bin2hex(random_bytes(8)));
            if (in_array($data->getRoles()[0], [User::ROLE_USER, User::ROLE_COMPANY_ADMIN]) && empty($data->getCompany())) {
                throw new \InvalidArgumentException('The Company field is required.');
            }

            $this->entityManager->persist($data);
            $this->entityManager->flush();
            return $data;
        }
        if ($operation instanceof Patch) {
            $userId = $uriVariables['id'];
            $user = $this->entityManager->getRepository(User::class)->find($userId);
            if (!$user) {
                throw new \InvalidArgumentException('User not found.');
            }
            if ($user->getId() === $this->security->getUser()->getId()) {
                throw new \InvalidArgumentException('You cannot edit your own user.');
            }
            if (!in_array($data->getRoles()[0], [User::ROLE_USER, User::ROLE_COMPANY_ADMIN, User::ROLE_SUPER_ADMIN])) {
                throw new \InvalidArgumentException('invalid Role provided.');
            }
            if (
                $this->security->isGranted(User::ROLE_COMPANY_ADMIN) &&
                !in_array($data->getRoles()[0], [User::ROLE_USER, User::ROLE_COMPANY_ADMIN])
            ) {
                throw new \InvalidArgumentException('you are not allowed to grant this role!');
            }

            if ($data->getCompany()) {
                $user->setCompany($data->getCompany());
            }
            if ($data->getName()) {
                $user->setName($data->getName());
            }
            $user->setRole($data->getRoles()[0]);
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return new JsonResponse([
                'status' => 'success',
                'message' => 'User with id ' . $userId . ' has been updated.'
            ]);
        }
    }
}
