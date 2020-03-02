<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndexWithoutLogin()
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertTrue($client->getResponse()->isRedirection());
        $this->assertTrue($client->getResponse()->isRedirect('/login'));
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }
}
