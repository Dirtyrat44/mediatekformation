<?php

namespace App\tests\Repository;

use App\Entity\Playlist;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PlaylistRepositoryTest extends KernelTestCase {

    private const REPOSITORY_CLASS = \App\Repository\PlaylistRepository::class;

    public function testAddAndRemove() {
        self::bootKernel();
        $repository = static::getContainer()->get(self::REPOSITORY_CLASS);
        $title = 'Playlist add and remove';

        $playlist = new Playlist();
        $playlist->setName($title);
        $repository->add($playlist);

        $found = $repository->findOneBy(['name' => $title]);
        $this->assertNotNull($found);
        $this->assertEquals($playlist->getName(), $found->getName());

        $repository->remove($found);
        $notFound = $repository->findOneBy(['name' => $title]);
        $this->assertNull($notFound);
    }

    public function testAllOrderByName() {
        self::bootKernel();
        $repository = static::getContainer()->get(self::REPOSITORY_CLASS);

        $resultsASC = $repository->findAllOrderByName('ASC');
        $resultsDESC = $repository->findAllOrderByName('DESC');

        $filterASC = [];
        foreach ($resultsASC as $playlist) {
            $filterASC[] = $playlist->getName();
        }
        $expectedASC = $filterASC;
        sort($expectedASC);

        $filterDESC = [];
        foreach ($resultsDESC as $playlist) {
            $filterDESC[] = $playlist->getName();
        }
        $expectedDESC = $filterDESC;
        rsort($expectedDESC);

        $this->assertEquals($expectedASC, $filterASC);
        $this->assertEquals($expectedDESC, $filterDESC);
    }

    public function testFindAllOrderByFormationCount() {
        self::bootKernel();
        $repository = static::getContainer()->get(self::REPOSITORY_CLASS);

        $resultsASC = $repository->findAllOrderByFormationCount('ASC');
        $resultsDESC = $repository->findAllOrderByFormationCount('DESC');

        $filterASC = [];
        foreach ($resultsASC as $playlist) {
            $filterASC[] = $playlist->getFormationCount();
        }
        $expectedASC = $filterASC;
        sort($expectedASC);

        $filterDESC = [];
        foreach ($resultsDESC as $playlist) {
            $filterDESC[] = $playlist->getFormationCount();
        }
        $expectedDESC = $filterDESC;
        rsort($expectedDESC);

        $this->assertEquals($expectedASC, $filterASC);
        $this->assertEquals($expectedDESC, $filterDESC);
    }

    public function testFindByContainValue() {
        self::bootKernel();
        $repository = static::getContainer()->get(self::REPOSITORY_CLASS);

        $resultsOne = $repository->findByContainValue('name', '0');
        $resultsTwo = $repository->findByContainValue('name', '');

        $this->assertNotEmpty($resultsTwo);

        foreach ($resultsOne as $playlist) {
            $this->assertStringContainsString('0', $playlist->getName());
        }

        $isSortedASC = [];
        foreach ($resultsTwo as $playlist) {
            $isSortedASC[] = $playlist->getName();
        }
        $expectedASC = $isSortedASC;
        sort($expectedASC);

        $this->assertEquals($expectedASC, $isSortedASC);
    }

    public function testFindByContainValueWithTable() {
        self::bootKernel();
        $repository = static::getContainer()->get(self::REPOSITORY_CLASS);

        $results = $repository->findByContainValue('name', '0', 'categories');

        $this->assertNotEmpty($results);

        foreach ($results as $playlist) {
            $found = false;
            foreach ($playlist->getFormations() as $formation) {
                foreach ($formation->getCategories() as $categorie) {
                    if (str_contains($categorie->getName(), '0')) {
                        $found = true;
                        break 2; // Revient au premier foreach
                    }
                }
            }
            $this->assertTrue($found);
        }
        $isSortedASC = [];
        foreach ($results as $playlist) {
            $isSortedASC[] = $playlist->getName();
        }
        $expectedASC = $isSortedASC;
        sort($expectedASC);
        $this->assertEquals($expectedASC, $isSortedASC);
    }
}
