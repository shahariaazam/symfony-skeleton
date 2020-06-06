<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UsersControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = AuthControllerTest::getAuthenticatedClient();
        $client->request('GET', '/admin/users');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testUserDetails()
    {
        $client = AuthControllerTest::getAuthenticatedClient();

        $em = $client->getContainer()->get('doctrine.orm.entity_manager');
        $userRepository = $em->getRepository(User::class);
        $user = $userRepository->findOneBy(['email' => 'admin@example.com']);

        $client->request('GET', '/admin/users/'.$user->getUuid());
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testUserDetailsWithInvalidUUID()
    {
        $client = AuthControllerTest::getAuthenticatedClient();
        $client->request('GET', '/admin/users/FAKE_UUID');
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }
}
