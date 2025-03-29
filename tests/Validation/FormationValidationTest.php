<?php

namespace App\Tests\Validation;

use App\Entity\Playlist;
use App\Entity\Formation;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @author arthur poncin
 */
class FormationValidationTest extends KernelTestCase
{
    public function testPublishedAtCannotBeAfterToday()
    {
        self::bootKernel(); // Lance l'env Symfony
        $validator = static::getContainer()->get(ValidatorInterface::class); // Recup le validateur Symfony
        
        $formation = new Formation();
        $playlist = new Playlist();
        $formation->setTitle('titre');
        $formation->setVideoId('12345678910');
        $formation->setPlaylist($playlist);
        $formation->setPublishedAt(new \DateTime('+5 days'));
        
        $errors = $validator->validate($formation);
        $this->assertGreaterThan(0, count($errors));
    }
    
    public function testPublishedAtCanBeBeforeToday()
    {
        self::bootKernel(); // Lance l'env Symfony
        $validator = static::getContainer()->get(ValidatorInterface::class); // Recup le validateur Symfony
        
        $formation = new Formation();
        $playlist = new Playlist();
        $formation->setTitle('titre');
        $formation->setVideoId('12345678911');
        $formation->setPlaylist($playlist);
        $formation->setPublishedAt(new \DateTime('-5 days'));
        
        $errors = $validator->validate($formation);
        $this->assertEquals(0, count($errors));
    }
    
    public function testPublishedAtCannotBeBlank()
    {
        self::bootKernel(); // Lance l'env Symfony
        $validator = static::getContainer()->get(ValidatorInterface::class); // Recup le validateur Symfony
        
        $formation = new Formation();
        $playlist = new Playlist();
        $formation->setTitle('titre');
        $formation->setVideoId('12345678912');
        $formation->setPlaylist($playlist);
        
        $errors = $validator->validate($formation);
        $this->assertGreaterThan(0, count($errors));
    }
}