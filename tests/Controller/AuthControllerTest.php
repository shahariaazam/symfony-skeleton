<?php
/**
 * AuthControllerTest class.
 */

namespace App\Tests\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class AuthControllerTest extends WebTestCase
{
    public function testLoginRoute()
    {
        $client = static::createClient();
        $client->request('GET', '/login');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testSignupRoute()
    {
        $client = static::createClient();
        $client->request('GET', '/signup');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testLoginWithGoogleRoute()
    {
        $client = static::createClient();
        $client->request('GET', '/login/google');

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }

    public function testLogoutRouteWithoutLogin()
    {
        $client = static::createClient();
        $client->request('GET', '/logout');

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }

    public function testLogoutRouteAfterLogin()
    {
        $client = AuthControllerTest::getAuthenticatedClient();
        $client->request('GET', '/logout');

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }

    /**
     * Test successful signup and auto login to dashboard.
     */
    public function testSuccessfulSignupAndAutoLoginToDashboard()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/signup');
        $buttonCrawlerNode = $crawler->selectButton('Create Account');

        // Passing known (data added by Fixture) values to test
        $form = $buttonCrawlerNode->form([
            'registration_form[first_name]' => 'Hello',
            'registration_form[last_name]' => 'Symfony',
            'registration_form[email]' => 'hello-symfony@example.com',
            'registration_form[is_tos_accepted]' => true,
            'registration_form[password]' => 'password',
        ], 'POST');

        $client->submit($form);
        $this->assertTrue($client->getResponse()->isRedirect());

        $crawler = $client->followRedirect();

        // After signup it will redirect to dashboard with auto-login
        $this->assertContains('Logout', $crawler->text());
    }

    /**
     * Test successful login and after login it will redirect to main dashboard page.
     */
    public function testLoginSuccessfully()
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $crawler = $client->followRedirect();
        $buttonCrawlerNode = $crawler->selectButton('Sign in');

        // Passing known (data added by Fixture) values to test
        $form = $buttonCrawlerNode->form([
            'email' => 'admin@example.com',
            'password' => 'password',
        ], 'POST');

        $client->submit($form);
        $this->assertTrue($client->getResponse()->isRedirect());

        $crawler = $client->followRedirect();

        // Login completed and went to the original URL
        $this->assertContains('Logout', $crawler->text());
    }

    /**
     * Get authenticated client for further functional test.
     *
     * @param null $client
     *
     * @return KernelBrowser|null
     */
    public static function getAuthenticatedClient(User $user = null, $client = null)
    {
        if (empty($client)) {
            $client = static::createClient();
        }
        $session = $client->getContainer()->get('session');

        if (empty($user)) {
            $em = $client->getContainer()->get('doctrine.orm.entity_manager');
            $userRepository = $em->getRepository(User::class);
            $user = $userRepository->findOneBy(['email' => 'admin@example.com']);
        }

        $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
        $session->set('_security_main', serialize($token));
        $session->save();

        $client->getCookieJar()->set(new Cookie($session->getName(), $session->getId()));

        return $client;
    }
}
