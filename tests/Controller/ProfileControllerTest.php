<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProfileControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = AuthControllerTest::getAuthenticatedClient();
        $client->request('GET', '/profile');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
