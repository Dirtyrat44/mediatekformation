<?php

namespace App\tests\Repository;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Entity\Categorie;

/**
 * Tests du repository CategorieRepository
 *
 * Vérifie l'ajout et la suppression d'une catégorie,
 * ainsi que la récupération des catégories liées à une playlist
 *
 * @author arthurponcin
 */
class CategorieRepositoryTest extends KernelTestCase {

    private const REPOSITORY_CLASS = \App\Repository\CategorieRepository::class;

    public function testAddAndRemove() {
        self::bootKernel();
        $repository = static::getContainer()->get(self::REPOSITORY_CLASS);
        $name = 'Categorie add and remove';

        $categorie = new Categorie();
        $categorie->setName($name);
        $repository->add($categorie);

        $found = $repository->findOneBy(['name' => $name]);

        $this->assertNotNull($found);
        $this->assertEquals($found->getName(), $categorie->getName());

        $repository->remove($found);
        $notFound = $repository->findOneBy(['name' => $name]);
        $this->assertNull($notFound);
    }

    public function testFindAllForOnePlaylist() {
        self::bootKernel();
        $repository = static::getContainer()->get(self::REPOSITORY_CLASS);

        $results = $repository->findAllForOnePlaylist(43);

        $this->assertNotNull($results);
        $this->assertNotEmpty($results);

        $listCategories = [];
        foreach ($results as $categorie) {
            $listCategories[] = $categorie->getName();
        }

        $expected = $listCategories;
        sort($expected);

        $this->assertEquals($expected, $listCategories);
    }
}
