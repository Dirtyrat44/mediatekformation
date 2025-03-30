<?php

namespace App\tests\Repository;

use App\Entity\Playlist;
use App\Entity\Formation;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Tests du repository FormationRepository
 *
 * Vérifie les méthodes d’ajout, suppression, tri, recherche par champ ou relation
 * Couvre également les méthodes spécifiques comme findAllLatest ou findAllForOnePlaylist
 *
 * @author arthurponcin
 */
class FormationRepositoryTest extends KernelTestCase
{
    private const REPOSITORY_CLASS = \App\Repository\FormationRepository::class;
    
    public function testfindAllLatest()
    {
        self::bootKernel();
        $repository = static::getContainer()->get(self::REPOSITORY_CLASS);
        $formation = $repository->findAllLatest(1);
        
        $this->assertCount(1, $formation);
        $this->assertInstanceof(Formation::class, $formation[0]);
        $this->assertEquals('Formation test 0', $formation[0]->getTitle());
    }
    
    public function testFindAllLatestWithoutData()
    {
        self::bootKernel();
        $repository = static::getContainer()->get(self::REPOSITORY_CLASS);
        
        $test = $repository->findAllLatest(0);

        $this->assertCount(0, $test);
    }
    
    public function testAddAndRemove()
    {
        $title = 'Formation de test avec add et remove';
        self::bootKernel();
        $repository = static::getContainer()->get(self::REPOSITORY_CLASS);
        $formation = new Formation();
        $formation->setTitle($title);
        
        $repository->add($formation);
        
        $found = $repository->findOneBy(['title' => $title]);
        
        $this->assertInstanceOf(Formation::class, $found); // Test add
        $this->assertEquals($title, $found->getTitle());
        $this->assertNotNull($found);
        
        $repository->remove($formation);
        $foundRemove = $repository->findOneBy(['title' => $title]);
        
        $this->assertNull($foundRemove); // Test remove
    }
    
    public function testFindAllOrderBy()
    {
        self::bootKernel();
        $repository = static::getContainer()->get(self::REPOSITORY_CLASS);
        
        $results = $repository->findAllOrderBy('title', 'ASC');
        $resultsDESC = $repository->findAllOrderBy('title', 'DESC');
        $this->assertNotEmpty($resultsDESC); // Test non blank
        $this->assertNotEmpty($results); // Test non blank
        
        $isSorted = [];
        foreach ($results as $result) {
            $isSorted[] = $result->getTitle();
        }
        $sorted = $isSorted;
        sort($sorted);
        
        $this->assertEquals($sorted, $isSorted); // Test ASC
       
        $isNotSorted = [];
        foreach ($resultsDESC as $resultDESC) {
            $isNotSorted[] = $resultDESC->getTitle();
        }
        $sortedDESC = $isNotSorted;
        rsort($sortedDESC);
        
        $this->assertEquals($sortedDESC, $isNotSorted); // Test DESC
    }
    
    public function testFindAllOrderByWithTable()
    {
        self::bootKernel();
        $repository = static::getContainer()->get(self::REPOSITORY_CLASS);
        
        $resultsASC = $repository->findAllOrderBy('name', 'ASC', 'categories');
        $resultsDESC = $repository->findAllOrderBy('name', 'DESC', 'categories');
        
        $isSortedASC = [];
        foreach ($resultsASC as $isSorted) {
            $firstCategoryASC = $isSorted->getCategories()->first(); // Collection donc sépare
            $isSortedASC[] = $firstCategoryASC ? $firstCategoryASC->getName() : ''; // Si null ''
        }
        
        $isSortedDESC = [];
        foreach ($resultsDESC as $isSorted) {
            $firstCategoryDESC = $isSorted->getCategories()->first(); // Collection donc sépare
            $isSortedDESC[] = $firstCategoryDESC ? $firstCategoryDESC->getName() : ''; // Si null ''
        }
        
        $expectedDESC = $isSortedDESC;
        $expectedASC = $isSortedASC;
        sort($expectedASC);
        rsort($expectedDESC);
        
        $this->assertEquals($expectedASC, $isSortedASC); // Test ASC
        $this->assertEquals($expectedDESC, $isSortedDESC); // Test DESC
    }
    
    public function testFindContainValue()
    {
        self::bootKernel();
        $repository = static::getContainer()->get(self::REPOSITORY_CLASS);
        
        $resultsOne = $repository->findByContainValue('description', '10');
        $resultsTwo = $repository->findByContainValue('description', '');
        
        foreach ($resultsOne as $formation) {
            $this->assertStringContainsString('0', $formation->getDescription());
        }
        
        $isSortedDESC = [];
        foreach ($resultsTwo as $formation) {
            $isSortedDESC[] = $formation->getPublishedAt();
        }
        
        $expectedDESC = $isSortedDESC;
        rsort($expectedDESC);
        
        $this->assertCount(1, $resultsOne); // Une seule formation avec description 0
        $this->assertGreaterThan(count($resultsOne), count($resultsTwo)); // Results two = toute la collection
        $this->assertEquals($expectedDESC, $isSortedDESC); // Test DESC publishedAT
    }
    
    public function testFindContainValueWithTable()
    {
        self::bootKernel();
        $repository = static::getContainer()->get(self::REPOSITORY_CLASS);
        $em = static::getContainer()->get('doctrine')->getManager(); // Pour intéragir avec BDD
        $title = 'à tester';
        
        
        $playlist = new Playlist();
        $playlist->setName($title);
        $em->persist($playlist);
        $formation = new Formation();
        $formation->setTitle('Formation test playlist');
        $formation->setPublishedAt(new \DateTime());
        $formation->setPlaylist($playlist);
        $em->persist($formation);
        
        $em->flush();
        
        $results = $repository->findByContainValue('name', $title, 'playlist');
        
        $this->assertNotNull($results);
        $this->assertCount(1, $results);
        
        $isSortedDESC = [];
        foreach ($results as $result) {
            $isSortedDESC[] = $result->getPublishedAt();
        }
        $expectedDESC = $isSortedDESC;
        rsort($expectedDESC);
        
        $this->assertEquals($expectedDESC, $isSortedDESC); // Test DESC
        
        $em->remove($playlist);
        $repository->remove($formation); // Nettoie la BDD
        $em->flush();
    }
    
    public function testFindAllForOnePlaylist()
    {
        self::bootKernel();
        $repository = static::getContainer()->get(self::REPOSITORY_CLASS);
        
        
        $results = $repository->findAllForOnePlaylist(38);
        
        $sortedASC = [];
        foreach ($results as $formation) {
            $sortedASC[] = $formation->getPublishedAt();
        }
        $expectedASC = $sortedASC;
        sort($expectedASC);
        
        $this->assertEquals($expectedASC, $sortedASC);
    }
}