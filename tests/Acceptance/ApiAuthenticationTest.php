<?php

namespace App\Tests\Acceptance;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\ApiToken;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class ApiAuthenticationTest extends ApiTestCase
{
    use ResetDatabase, Factories;

    private $client;
    private $entityManager;

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
        $apiToken->setToken('valid_token');
        $apiToken->setUser($user);

        $this->entityManager->persist($user);
        $this->entityManager->persist($apiToken);
        $this->entityManager->flush();
    }

    public function test_api_reject_in_case_of_empty_token()
    {
        $this->client->request('GET', '/api/', [
            'headers' => [
                'x-api-token' => '',
            ],
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    public function test_api_reject_in_case_of_not_set_token()
    {
        $this->client->request('GET', '/api/');
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    public function test_api_reject_in_case_of_invalid_api_token()
    {
        $this->client->request('GET', '/api/',[
            'headers' => [
                'x-api-token' => 'SOMETHING_WRONG',
            ]
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
        $this->assertJsonContains([
            'message' => 'Bad credentials.'
        ]);
    }


    public function test_api_accept_in_case_of_correct_token()
    {
        $this->client->request('GET', '/api/',[
            'headers' => [
                'x-api-token' => 'valid_token',
            ],
        ]);
        $this->assertResponseRedirects();
    }
}