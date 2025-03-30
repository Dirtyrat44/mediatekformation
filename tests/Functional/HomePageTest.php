<?php

namespace App\tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomePageTest extends WebTestCase
{
    /**
    * Test fonctionnel de la page d'accueil
    *
    * Vérifie que la page d'accueil du site est accessible et que
    * le message de bienvenue est bien affiché dans le titre principal
    *
    * @author arthurponcin
    */
    public function testHomePageIsAccessible(): void
    {
        $client = static::createClient(); // Crée un client HTTP
        $client->request('GET', '/');
        $this->assertResponseIsSuccessful(); // 200 = Ok
        $this->assertSelectorExists('h3');
        $this->assertSelectorTextContains('h3', 'Bienvenue');
    }
}