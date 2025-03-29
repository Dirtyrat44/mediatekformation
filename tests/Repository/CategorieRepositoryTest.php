<?php

namespace App\tests\Repository;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Entity\Categorie;

class CategorieRepositoryTest extends KernelTestCase {

    private const REPOSITORY_CLASS = \App\Repository\CategorieRepository::class;

    public function testAddAndRemove() {
        self::bootKernel();
        $repository = static::getContainer()->get(self::REPOSITORY_CLASS);

        $categorie = new Categorie();
        $categorie->setName('Categorie add and remove');
        $repository->add($categorie);

        $found = $repository->findOneBy(['name' => 'Categorie add and remove']);

        $this->assertNotNull($found);
        $this->assertEquals($found->getName(), $categorie->getName());

        $repository->remove($found);
        $notFound = $repository->findOneBy(['name' => 'Categorie add and remove']);
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
