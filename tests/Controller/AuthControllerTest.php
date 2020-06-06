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
            'registration_form[password][first]' => 'password',
            'registration_form[password][second]' => 'password',
        ], 'POST');

        $client->submit($form);
        $this->assertTrue($client->getResponse()->isRedirect());

        $crawler = $client->followRedirect();

        // After signup it will redirect to dashboard with auto-login
        $this->assertContains('Login', $crawler->text());
    }

    /**
     * Test successful login and after login it will redirect to main dashboard page.
     */
    public function testLoginSuccessfully()
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $crawler = $client->followRedirect();
        $buttonCrawlerNode = $crawler->selectButton('Login');

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
     * Test successful login and after login it will redirect to main dashboard page.
     */
    public function testLoginSuccessfullyWithRememberMe()
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $crawler = $client->followRedirect();
        $buttonCrawlerNode = $crawler->selectButton('Login');

        // Passing known (data added by Fixture) values to test
        $form = $buttonCrawlerNode->form([
            'email' => 'admin@example.com',
            'password' => 'password',
            '_remember_me' => 'on',
        ], 'POST');

        $client->submit($form);
        $this->assertTrue($client->getResponse()->isRedirect());

        $crawler = $client->followRedirect();

        $this->assertBrowserHasCookie('REMEMBERME');

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

    /**
     * @param KernelBrowser $client
     * @param $path
     *
     * @param array $roles
     * @return int
     */
    public static function checkPathIsAccessibleByRoles(KernelBrowser $client, $path, array $roles)
    {
        $em = $client->getContainer()->get('doctrine.orm.entity_manager');
        $userRepository = $em->getRepository(User::class);

        $user = $userRepository->findOneBy(['email' => 'admin@example.com']);
        $user->setRoles($roles);

        $client = AuthControllerTest::getAuthenticatedClient($user, $client);
        $client->request('GET', $path);

        return $client->getResponse()->getStatusCode();
    }

    public function testResetPasswordShouldRedirectToChangePasswordIfUserAlreadyLoggedIn()
    {
        $client = $this->getAuthenticatedClient();
        $client->request('GET', '/reset-password');

        $this->assertTrue($client->getResponse()->isRedirect('/change-password'));

        $crawler = $client->followRedirect();
        $this->assertContains('You were already logged in', $crawler->text());
    }

    public function testResetPasswordInitialRequest()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/reset-password');

        $buttonCrawlerNode = $crawler->selectButton('Reset Password');

        // Passing known (data added by Fixture) values to test
        $form = $buttonCrawlerNode->form([
            'reset_password[email]' => 'admin@example.com',
        ], 'POST');

        $client->submit($form);
        $this->assertTrue($client->getResponse()->isRedirect('/login'));

        $crawler = $client->followRedirect();
        $this->assertContains('Password reset instruction has been sent to your inbox', $crawler->text());
    }

    public function testResetPasswordInitialRequestWithInvalidEmail()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/reset-password');

        $buttonCrawlerNode = $crawler->selectButton('Reset Password');

        // Passing known (data added by Fixture) values to test
        $form = $buttonCrawlerNode->form([
            'reset_password[email]' => 'fake@email.com',
        ], 'POST');

        $client->submit($form);
        $this->assertTrue($client->getResponse()->isRedirect('/login'));

        $crawler = $client->followRedirect();
        $this->assertContains('No email address found in our system', $crawler->text());
    }

    public function testResetPasswordWithValidToken()
    {
        $client = static::createClient();

        $em = $client->getContainer()->get('doctrine.orm.entity_manager');
        $userRepository = $em->getRepository(User::class);

        /**
         * @var $user User
         */
        $user = $userRepository->findOneBy(['email' => 'admin@example.com']);
        $user->setResetPasswordToken('abc');
        $em->persist($user);
        $em->flush();

        $crawler = $client->request('GET', '/reset-password?token='.$user->getResetPasswordToken());

        $buttonCrawlerNode = $crawler->selectButton('Set New Password');

        // Passing known (data added by Fixture) values to test
        $form = $buttonCrawlerNode->form([
            'set_password_public[plain_password][first]' => 'password',
            'set_password_public[plain_password][second]' => 'password',
            'set_password_public[reset_password_token]' => $user->getResetPasswordToken(),
        ], 'POST');

        $client->submit($form);

        $this->assertTrue($client->getResponse()->isRedirect('/login'));

        $crawler = $client->followRedirect();
        $this->assertContains('Password has been changed successfully', $crawler->text());
    }

    public function testResetPasswordWithInvalidToken()
    {
        $client = static::createClient();

        $client->request('GET', '/reset-password?token=FAKE_TOKEN');

        $this->assertTrue($client->getResponse()->isRedirect('/login'));

        $crawler = $client->followRedirect();
        $this->assertContains('Invalid token', $crawler->text());
    }
}
