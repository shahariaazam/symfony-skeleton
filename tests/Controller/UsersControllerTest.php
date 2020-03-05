<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UsersControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = AuthControllerTest::getAuthenticatedClient();
        $client->request('GET', '/admin/users');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
