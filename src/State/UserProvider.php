<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class UserProvider implements ProviderInterface
{
    public function __construct(private EntityManagerInterface $entityManager, private Security $security)
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if ($this->security->isGranted('ROLE_SUPER_ADMIN')) {
            return $this->entityManager->getRepository(User::class)->findAll();
        }

        if (
            $this->security->isGranted('ROLE_USER') ||
            $this->security->isGranted('ROLE_COMPANY_ADMIN')
        ) {
            $user = $this->security->getUser();
            if ($user instanceof User && $user->getCompany()) {
                return $this->entityManager->getRepository(User::class)
                    ->findBy(['company' => $user->getCompany()]);
            }
        }

        return [];
    }
}
