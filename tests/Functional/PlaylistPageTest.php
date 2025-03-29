<?php

namespace App\tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PlaylistPageTest extends WebTestCase
{

    private $client;

    private const TEMPLATE_CSS = 'table tbody tr:first-child td';
    private const TEMPLATE_ALLLINES = 'table tbody tr';

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testPlaylistsAreSortedByName(): void
    {
        $crawlerASC = $this->client->request('GET', '/playlists/tri/name/ASC');
        $crawlerDESC = $this->client->request('GET', '/playlists/tri/name/DESC');

        $firstNameASC = $crawlerASC->filter(self::TEMPLATE_CSS)->first()->text();
        $firstNameDESC = $crawlerDESC->filter(self::TEMPLATE_CSS)->first()->text();

        $this->assertStringContainsString('PlaylistTest 0', $firstNameASC);
        $this->assertStringContainsString('PlaylistTest 3', $firstNameDESC);
    }

    public function testFilterByCategory(): void
    {
        $crawler = $this->client->request('POST', '/playlists/recherche/id/categories', ['recherche' => 37]);

        $this->assertResponseIsSuccessful();
        $firstCategorie = $crawler->filter(self::TEMPLATE_CSS)->eq(1)->text();
        $this->assertStringContainsString('Categorie test 0', $firstCategorie);
        
        $playlistCategorie = $crawler->filter('table tbody tr');
        $this->assertCount(2, $playlistCategorie);
    }

    public function testPlaylistsAreSortedByFormationCount(): void
    {
        $crawlerDESC = $this->client->request('GET', '/playlists/tri/formations/DESC');
        $crawlerASC = $this->client->request('GET', '/playlists/tri/formations/ASC');

        $firstNameDESC = $crawlerDESC->filter(self::TEMPLATE_CSS)->eq(2)->text();
        $firstNameASC = $crawlerASC->filter(self::TEMPLATE_CSS)->eq(2)->text();

        $this->assertStringContainsString(4, $firstNameDESC);
        $this->assertStringContainsString(3, $firstNameASC);
    }

    public function testFilterByPlaylistName(): void
    {
        $crawler = $this->client->request('POST', '/playlists/recherche/name', ['recherche' => 'PlaylistTest 2']);

        $this->assertResponseIsSuccessful();
        $firstPlaylist = $crawler->filter(self::TEMPLATE_CSS)->first()->text();
        $this->assertStringContainsString('PlaylistTest 2', $firstPlaylist);

        $crawlerBlank = $this->client->request('POST', '/playlists/recherche/name', ['recherche' => '']);
        $playlists = $crawlerBlank->filter(self::TEMPLATE_ALLLINES);
        $this->assertCount(4, $playlists);
    }
    
    public function testViewPlaylistDetails(): void
    {
        $crawler = $this->client->request('GET', '/playlists');
        $link = $crawler->filter('table tbody tr:first-child td a')->link();
        $this->client->click($link);
        
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('h4');
        $this->assertSelectorTextContains('h4', 'PlaylistTest 0');
    }
}
