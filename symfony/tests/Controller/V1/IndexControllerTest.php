<?php

namespace App\Tests\Controller\V1;

use App\Controller\V1\IndexController;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class IndexControllerTest extends WebTestCase
{
    protected function createAuthenticatedClient($username = 'user', $password = 'password')
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/login_check',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                '_username' => $username,
                '_password' => $password,
            ])
        );

        $data = json_decode($client->getResponse()->getContent(), true);

        $client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $data['token']));

        return $client;
    }

    public function testIndex()
    {
        $client = static::createClient();
        $client->request('GET', '/api/v1/');
        self::assertResponseStatusCodeSame(401, $client->getResponse()->getStatusCode());
    }
}
