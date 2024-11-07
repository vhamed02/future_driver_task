<?php

namespace App\Tests\Acceptance;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\ApiToken;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;

class UserTest extends ApiTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();
        $this->entityManager = $this->client->getContainer()->get('doctrine.orm.entity_manager');

        $user = new User();
        $user->setName("Hamed");
        $user->setRole('ROLE_SUPER_ADMIN');
        $user->setPassword('qwe123');

        $apiToken = new ApiToken();
        $apiToken->setToken('super_admin_user_valid_token');
        $apiToken->setUser($user);

        $this->entityManager->persist($user);
        $this->entityManager->persist($apiToken);
        $this->entityManager->flush();
    }

    public function test_a_super_admin_user_can_insert_a_new_company()
    {
        $this->client->request('POST', '/api/users', [
            'headers' => [
                'x-api-token' => 'super_admin_user_valid_token',
            ],
            'json' => [
                'name' => 'Hamed',
                'role' => 'ROLE_USER',
                'company' => 'api/companies/1'
            ]
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertJsonContains([
            'name' => 'Hamed'
        ]);
    }
}