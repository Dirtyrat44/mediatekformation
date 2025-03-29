<?php

namespace App\tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\Admin;

class AdminCategoriePageTest extends WebTestCase
{
    private $client;
    private $entityManager;
    private $adminUser;
    
    private const TEMPLATE_CSS = 'table tbody tr:first-child td';
    private const TEMPLATE_ALLLINES = 'table tbody tr';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $passwordHasher = static::getContainer()->get(UserPasswordHasherInterface::class);

        // Vérif si User existe
        $userRepository = $this->entityManager->getRepository(Admin::class);
        $this->adminUser = $userRepository->findOneBy(['username' => 'admin']);

        if (!$this->adminUser) {
            $this->adminUser = new Admin();
            $this->adminUser->setUsername('admin');
            $this->adminUser->setRoles(['ROLE_ADMIN']);
            $hashedPassword = $passwordHasher->hashPassword($this->adminUser, 'motdepasseadmin');
            $this->adminUser->setPassword($hashedPassword);

            $this->entityManager->persist($this->adminUser);
            $this->entityManager->flush();
        }

        $this->client->loginUser($this->adminUser);
    }
    
    public function testAdminCategorieIsAccessible(): void
    {
        $url = '/admin/categorie/';
        $this->client->request('GET', $url);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('div', "Accueil Formations Playlists Catégories Déconnexion");
    }
    
    public function testLogout(): void
    {
        $url = '/admin/categorie/';
        $titleCheck = 'Bienvenue sur le site de MediaTek86 consacré aux formations en ligne';
        $crawler = $this->client->request('GET', $url);
        
        $link = $crawler->filter('body div nav li a[title^="Se déconnecter"]')->link();
        $this->client->click($link);
        
        $this->client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('h3');
        $this->assertSelectorTextContains('h3', $titleCheck);
        
        $this->client->request('GET', $url);
        $this->assertResponseRedirects('/login'); // Vérifie la redirection si bien logout
        $this->client->followRedirect();
        $this->assertSelectorExists('h1');
        $this->assertSelectorTextContains('h1', 'Connexion');
    }
}