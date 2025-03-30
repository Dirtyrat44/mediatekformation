<?php

namespace App\tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\Admin;

/**
 * Tests fonctionnels du backoffice des playlists
 *
 * Vérifie les fonctionnalités de la page admin des playlists :
 * tri par nom ou nombre de formations, filtres par catégorie ou nom,
 * et accès à la page de modification
 *
 * @author arthurponcin
 */
class AdminPlaylistPageTest extends WebTestCase
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
    
    public function testAdminPlaylistsAreSortedByName(): void
    {
        $crawlerASC = $this->client->request('GET', '/admin/playlist/tri/name/ASC');
        $crawlerDESC = $this->client->request('GET', '/admin/playlist/tri/name/DESC');

        $firstNameASC = $crawlerASC->filter(self::TEMPLATE_CSS)->first()->text();
        $firstNameDESC = $crawlerDESC->filter(self::TEMPLATE_CSS)->first()->text();

        $this->assertStringContainsString('PlaylistTest 0', $firstNameASC);
        $this->assertStringContainsString('PlaylistTest 3', $firstNameDESC);
    }
    
    public function testAdminPlaylistsAreSortedByFormationCount(): void
    {
        $crawlerDESC = $this->client->request('GET', '/admin/playlist/tri/formations/DESC');
        $crawlerASC = $this->client->request('GET', '/admin/playlist/tri/formations/ASC');

        $firstNameDESC = $crawlerDESC->filter(self::TEMPLATE_CSS)->eq(2)->text();
        $firstNameASC = $crawlerASC->filter(self::TEMPLATE_CSS)->eq(2)->text();

        $this->assertStringContainsString(4, $firstNameDESC);
        $this->assertStringContainsString(3, $firstNameASC);
    }
    
    public function testAdminFilterByCategory(): void
    {
        $crawler = $this->client->request('POST', '/admin/playlist/recherche/id/categories', ['recherche' => 37]);

        $this->assertResponseIsSuccessful();
        $firstCategorie = $crawler->filter(self::TEMPLATE_CSS)->eq(1)->text();
        $this->assertStringContainsString('Categorie test 0', $firstCategorie);
        
        $playlistCategorie = $crawler->filter(self::TEMPLATE_ALLLINES);
        $this->assertCount(2, $playlistCategorie);
    }
    
    public function testAdminFilterByPlaylistName(): void
    {
        $crawler = $this->client->request('POST', '/admin/playlist/recherche/name', ['recherche' => 'PlaylistTest 2']);

        $this->assertResponseIsSuccessful();
        $firstPlaylist = $crawler->filter(self::TEMPLATE_CSS)->first()->text();
        $this->assertStringContainsString('PlaylistTest 2', $firstPlaylist);

        $crawlerBlank = $this->client->request('POST', '/admin/playlist/recherche/name', ['recherche' => '']);
        $playlists = $crawlerBlank->filter(self::TEMPLATE_ALLLINES);
        $this->assertCount(4, $playlists);
    }
    
    public function testadminViewPlaylistDetails(): void
    {
        $crawler = $this->client->request('GET', '/admin/playlist/');
        $link = $crawler->filter('table tbody tr:first-child a[title^="Modifier"]')->link();
        $this->client->click($link);
        
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('h1');
        $this->assertSelectorTextContains('h1', 'Modifier la playlist');
    }
}