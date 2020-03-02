<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    /**
     * Test dashboard page without login.
     */
    public function testIndexWithoutLogin()
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertTrue($client->getResponse()->isRedirection());
        $this->assertTrue($client->getResponse()->isRedirect('/login'));
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }

    /**
     * Test get access to dashboard after successful login.
     */
    public function testIndexWithLogin()
    {
        $client = AuthControllerTest::getAuthenticatedClient();
        $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
