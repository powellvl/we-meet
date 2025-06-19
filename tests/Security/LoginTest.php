<?php

namespace App\Tests\Security;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoginTest extends WebTestCase
{
    public function testUserCanLogin(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Se connecter')->form([
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $client->submit($form);

        $this->assertResponseRedirects('/home');

        $client->followRedirect();
        $this->assertSelectorTextContains('body', 'Bienvenue');
    }
}

?>