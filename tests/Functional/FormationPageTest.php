<?php

namespace App\tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FormationPageTest extends WebTestCase
{

    private $client;

    private const TEMPLATE_CSS = 'table tbody tr:first-child td';
    private const TEMPLATE_ALLLINES = 'table tbody tr';

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testFormationsAreSortedByTitle(): void
    {
        $crawlerASC = $this->client->request('GET', '/formations/tri/title/ASC');
        $crawlerDESC = $this->client->request('GET', '/formations/tri/title/DESC');

        $firstTitleASC = $crawlerASC->filter(self::TEMPLATE_CSS)->first()->text();
        $this->assertStringContainsString('Formation test 0', $firstTitleASC);

        $firstTitleDESC = $crawlerDESC->filter(self::TEMPLATE_CSS)->first()->text();
        $this->assertStringContainsString('Formation test 9', $firstTitleDESC);
    }

    public function testFormationAreSortedByPlaylist(): void
    {
        $crawlerASC = $this->client->request('GET', '/formations/tri/name/ASC/playlist');
        $crawlerDESC = $this->client->request('GET', '/formations/tri/name/DESC/playlist');

        // eq selectionne element n
        $firstNameASC = $crawlerASC->filter(self::TEMPLATE_CSS)->eq(1)->text();
        $this->assertStringContainsString('PlaylistTest 0', $firstNameASC);

        $firstNameDESC = $crawlerDESC->filter('table tbody tr:first-child td')->eq(1)->text();
        $this->assertStringContainsString('PlaylistTest 3', $firstNameDESC);
    }

    public function testFormationAreSortedByDate(): void
    {
        $crawlerASC = $this->client->request('GET', '/formations/tri/publishedAt/DESC');
        $crawlerDESC = $this->client->request('GET', '/formations/tri/publishedAt/ASC');

        $firstNameASC = $crawlerASC->filter(self::TEMPLATE_CSS)->eq(3)->text();
        $this->assertStringContainsString('27/03/2025', $firstNameASC);

        $firstNameDESC = $crawlerDESC->filter(self::TEMPLATE_CSS)->eq(3)->text();
        $this->assertStringContainsString('13/03/2025', $firstNameDESC);
    }

    public function testFilterByCategory(): void
    {
        $crawler = $this->client->request('POST', '/formations/recherche/id/categories', ['recherche' => 37]);

        $this->assertResponseIsSuccessful();
        $firstCategorie = $crawler->filter(self::TEMPLATE_CSS)->eq(2)->text();
        $this->assertStringContainsString('Categorie test 0', $firstCategorie);
        
        $formationCategorie = $crawler->filter(self::TEMPLATE_ALLLINES);
        $this->assertCount(3, $formationCategorie);
    }

    public function testFilterByFormationName(): void
    {
        $crawler = $this->client->request('POST', '/formations/recherche/title', ['recherche' => 'Formation test 14']);
        $this->assertResponseIsSuccessful();
        $firstTitle = $crawler->filter(self::TEMPLATE_CSS)->first()->text();
        $this->assertStringContainsString('Formation test 14', $firstTitle);
        
        $formationCount = $crawler->filter(self::TEMPLATE_ALLLINES);
        $this->assertCount(1, $formationCount);
    }
    

    public function testFilterByPlaylistName(): void
    {
        $crawler = $this->client->request('POST',
                '/formations/recherche/name/playlist',
                ['recherche' => 'PlaylistTest 2']
                );
        $this->assertResponseIsSuccessful();
        $firstPlaylist = $crawler->filter(self::TEMPLATE_CSS)->eq(1)->text();
        $this->assertStringContainsString('PlaylistTest 2', $firstPlaylist);
        
        $playlists = $crawler->filter(self::TEMPLATE_ALLLINES);
        $this->assertCount(4, $playlists);
    }
    
    public function testViewFormationDetails(): void
    {
        $crawler = $this->client->request('GET', '/formations');
        $link = $crawler->filter('table tbody tr:first-child td a')->link();
        $this->client->click($link);
        
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('h4');
        $this->assertSelectorTextContains('h4', 'Formation test 0');
    }
}
