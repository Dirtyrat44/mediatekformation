<?php

namespace App\DataFixtures;

use App\Entity\Formation;
use App\Entity\Playlist;
use App\Entity\Categorie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * Fixture pour insérer des données de test
 *
 * Sert à alimenter la base de données de test
 *
 * @author arthurponcin
 */
class AppFixtures extends Fixture {

     /**
     * Méthode appelée automatiquement pour insérer les données en base.
     *
     * @param ObjectManager $manager
     * @return void
     */
    public function load(ObjectManager $manager): void {
        $playlists = [];
        for ($i = 0; $i < 4; $i++) {
            $playlist = new Playlist();
            $playlist->setName('PlaylistTest ' . $i);
            $playlist->setDescription('Description Test ' . $i);
            $manager->persist($playlist);
            $playlists[] = $playlist;
        }

        $categories = [];
        for ($i = 0; $i < 6; $i++) {
            $categorie = new Categorie();
            $categorie->setName('Categorie test ' . $i);
            $manager->persist($categorie);
            $categories[] = $categorie;
        }

        for ($i = 0; $i < 15; $i++) {
            $formation = new Formation();
            $formation->setPlaylist($playlists[$i % count($playlists)]); // Distribue les playlists
            $formation->setDescription('Formation description test ' . $i);
            $formation->setPublishedAt(new \DateTime('-' . $i . ' days'));
            $formation->setVideoId('1234567891' . $i);
            $formation->setTitle('Formation test ' . $i);
            $formation->addCategory($categories[$i % count($categories)]); // Distribue les catégories
            $manager->persist($formation);
        }

        $manager->flush();
    }
}
