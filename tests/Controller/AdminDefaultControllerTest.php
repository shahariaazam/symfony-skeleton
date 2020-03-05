<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminDefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = AuthControllerTest::getAuthenticatedClient();
        $client->request('GET', '/admin');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
