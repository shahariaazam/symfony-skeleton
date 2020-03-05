<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

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
     * Test update general profile successfully.
     */
    public function testUpdateGeneralProfileExceptEmail()
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
        $this->assertNotContains('update@example.com', $crawler->text());
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

    public function testPublicUrl()
    {
        $client = AuthControllerTest::getAuthenticatedClient();
        $client->request('GET', '/p/symfony-administrator');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testProfilePictureUpload()
    {
        $client = AuthControllerTest::getAuthenticatedClient();
        $crawler = $client->request('GET', '/profile');

        // Update general profile data
        $photo = new UploadedFile(
            APP_ROOT_DIR.DIRECTORY_SEPARATOR.'public/assets/img/symfony_black_02.png',
            'symfony_black_02.png',
            'image/png',
            null
        );

        $buttonCrawlerNode = $crawler->selectButton('Change Picture');

        // Update general profile data
        $form = $buttonCrawlerNode->form([
            'profile_picture[picture]' => $photo,
        ], 'POST');

        $client->submit($form);

        $this->assertTrue($client->getResponse()->isRedirect('/profile'));
        $crawler = $client->followRedirect();

        $this->assertContains('Profile picture has been changed successfully', $crawler->text());
    }
}
