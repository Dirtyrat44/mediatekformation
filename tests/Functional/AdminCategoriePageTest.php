<?php

namespace App\tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\Admin;

/**
 * Test fonctionnel du back office des catégories
 *
 * Ces test vérifie que la page admin des catégories est bien accessible,
 * et que le processus de déconnexion redirige correctement l'utilisateur
 * @author arthurponcin
 */
class AdminCategoriePageTest extends WebTestCase
{
    /**
     * Client HTTP simule un navigateur
     * @var type
     */
    private $client;
    /**
     * EntityManager Doctrine pour accéder à la BDD
     * @var type
     */
    private $entityManager;
    private $adminUser;
  
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
    
    /**
     * Vérifie que la déconnexion fonctionne
     * L'utilisateur est redirigé sur la page d'accueil
     * l'accès au back office est refusé puis redirigé vers /login
     * @return void
     */
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