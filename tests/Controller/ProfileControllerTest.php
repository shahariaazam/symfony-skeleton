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

    /**
     * Test update general profile successfully.
     */
    public function testUpdateGeneralProfileSuccessfully()
    {
        $client = AuthControllerTest::getAuthenticatedClient();
        $crawler = $client->request('GET', '/profile');

        $buttonCrawlerNode = $crawler->selectButton('Update Profile');

        // Update general profile data
        $form = $buttonCrawlerNode->form([
            'profile[first_name]' => 'First name',
            'profile[last_name]' => 'Last name',
            'profile[email]' => 'update@example.com',
            'profile[gender]' => 1,
            'profile[timezone]' => 'America/New_York',
        ], 'POST');

        $client->submit($form);

        $client->getResponse()->isRedirect('/profile');
        $crawler = $client->followRedirect();

        $this->assertContains('Your profile has been updated', $crawler->text());
    }

    /**
     * Test password change.
     */
    public function testUpdatePasswordSuccessfully()
    {
        $client = AuthControllerTest::getAuthenticatedClient();
        $crawler = $client->request('GET', '/profile');

        $buttonCrawlerNode = $crawler->selectButton('Change Password');

        // Update general profile data
        $form = $buttonCrawlerNode->form([
            'password_update[password][first]' => '123456',
            'password_update[password][second]' => '123456',
        ], 'POST');

        $client->submit($form);

        $client->getResponse()->isRedirect('/profile');
        $crawler = $client->followRedirect();

        $this->assertContains('Your password has been updated', $crawler->text());
    }
}
