<?php

namespace App\tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\Admin;

class AdminFormationPageTest extends WebTestCase
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
    
    public function testAdminIsAccessible(): void
    {
        $this->client->request('GET', '/admin/formation/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('div', "Accueil Formations Playlists Catégories Déconnexion");
    }
    
    public function testAdminFormationsAreSortedByTitle(): void
    {
        $crawlerASC = $this->client->request('GET', '/admin/formation/tri/title/ASC');
        $crawlerDESC = $this->client->request('GET', '/admin/formation/tri/title/DESC');

        $firstTitleASC = $crawlerASC->filter(self::TEMPLATE_CSS)->first()->text();
        $this->assertStringContainsString('Formation test 0', $firstTitleASC);

        $firstTitleDESC = $crawlerDESC->filter(self::TEMPLATE_CSS)->first()->text();
        $this->assertStringContainsString('Formation test 9', $firstTitleDESC);
    }
    
     public function testAdminFormationAreSortedByPlaylist(): void
    {
        $crawlerASC = $this->client->request('GET', '/admin/formation/tri/name/ASC/playlist');
        $crawlerDESC = $this->client->request('GET', '/admin/formation/tri/name/DESC/playlist');
       
        $firstNameASC = $crawlerASC->filter(self::TEMPLATE_CSS)->eq(1)->text();
        $this->assertStringContainsString('PlaylistTest 0', $firstNameASC);

        $firstNameDESC = $crawlerDESC->filter(self::TEMPLATE_CSS)->eq(1)->text();
        $this->assertStringContainsString('PlaylistTest 3', $firstNameDESC);
    }
    
    public function testAdminFormationAreSortedByDate(): void
    {
        $crawlerASC = $this->client->request('GET', '/admin/formation/tri/publishedAt/DESC');
        $crawlerDESC = $this->client->request('GET', '/admin/formation/tri/publishedAt/ASC');

        $firstNameASC = $crawlerASC->filter(self::TEMPLATE_CSS)->eq(3)->text();
        $this->assertStringContainsString('27/03/2025', $firstNameASC);

        $firstNameDESC = $crawlerDESC->filter(self::TEMPLATE_CSS)->eq(3)->text();
        $this->assertStringContainsString('13/03/2025', $firstNameDESC);
    }
    
    public function testAdminFilterByCategory(): void
    {
        $crawler = $this->client->request('POST', '/admin/formation/recherche/id/categories', ['recherche' => 37]);

        $this->assertResponseIsSuccessful();
        $firstCategorie = $crawler->filter(self::TEMPLATE_CSS)->eq(2)->text();
        $this->assertStringContainsString('Categorie test 0', $firstCategorie);
        
        $formationCategorie = $crawler->filter(self::TEMPLATE_ALLLINES);
        $this->assertCount(3, $formationCategorie);
    }
    
    public function testAdminFilterByFormationName(): void
    {
        $crawler = $this->client->request(
                'POST', '/admin/formation/recherche/title', ['recherche' => 'Formation test 14']
                );
        $this->assertResponseIsSuccessful();
        $firstTitle = $crawler->filter(self::TEMPLATE_CSS)->first()->text();
        $this->assertStringContainsString('Formation test 14', $firstTitle);
        
        $formationCount = $crawler->filter(self::TEMPLATE_ALLLINES);
        $this->assertCount(1, $formationCount);
    }
    
    public function testAdminFilterByPlaylistName(): void
    {
        $crawler = $this->client->request('POST',
                '/admin/formation/recherche/name/playlist',
                ['recherche' => 'PlaylistTest 2']
                );
        $this->assertResponseIsSuccessful();
        $firstPlaylist = $crawler->filter(self::TEMPLATE_CSS)->eq(1)->text();
        $this->assertStringContainsString('PlaylistTest 2', $firstPlaylist);
        
        $playlists = $crawler->filter(self::TEMPLATE_ALLLINES);
        $this->assertCount(4, $playlists);
    }
    
    public function testAdminViewFormationEdit(): void
    {
        $crawler = $this->client->request('GET', '/admin/formation/');

        $link = $crawler->filter('table tbody tr:first-child a[title^="Modifier"]')->link();
        $this->client->click($link);
        
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('h1');
        $this->assertSelectorTextContains('h1', 'Modifier la formation');
    }
}