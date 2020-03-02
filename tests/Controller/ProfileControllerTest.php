<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProfileControllerTest extends WebTestCase
{
    public function testIndexWithoutLogin()
    {
        $client = static::createClient();
        $client->request('GET', '/profile');

        $this->assertTrue($client->getResponse()->isRedirection());
        $this->assertTrue($client->getResponse()->isRedirect('/login'));
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }
}
