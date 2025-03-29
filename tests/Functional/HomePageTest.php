<?php

namespace App\tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomePageTest extends WebTestCase
{
    public function testHomePageIsAccessible(): void
    {
        $client = static::createClient(); // Crée un client HTTP
        $client->request('GET', '/');
        $this->assertResponseIsSuccessful(); // 200 = Ok
        $this->assertSelectorExists('h3');
        $this->assertSelectorTextContains('h3', 'Bienvenue');
    }
}