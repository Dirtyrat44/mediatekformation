<?php

namespace App\tests\Entity;

use App\Entity\Formation;
use PHPUnit\Framework\TestCase;

/**
 * Test unitaire pour l'entité formation
 * 
 * @author arthur poncin
 */
class FormationTest extends TestCase
{
    /**
     * Teste le format de la date avec une heure précise
     * Date retournée sans l'heure
     * @return void
     */
    public function testGetPublishedAtStringWithTime(): void
    {
        $formation = new Formation();
        $date = new \DateTime('2020-01-04 17:00:12');
        $formation->setPublishedAt($date);

        $expected = '04/01/2020';
        $this->assertEquals($expected, $formation->getPublishedAtString());
    }
    
    /**
     * Teste la méthode avec une date en mars 2024
     * @return void
     */
    public function testGetPublishedAtStringWithMarchDate(): void
    {
        $formation = new Formation();
        $date = new \DateTime('2024-03-25');
        $formation->setPublishedAt($date);

        $expected = '25/03/2024';
        $this->assertEquals($expected, $formation->getPublishedAtString());
    }
    
    /**
     * Teste la méthode avec une date nulle : doit renvoyer une chaîne vide
     * @return void
     */
    public function testGetPublishedAtStringWithNullDate(): void
    {
        $formation = new Formation();
        $expected = '';
        $this->assertEquals($expected, $formation->getPublishedAtString());
    }
    
    /**
     * Teste la méthode avec une date ancienne
     * @return void
     */
    public function testGetPublishedAtStringWithLowerYearDate(): void
    {
        $formation = new Formation();
        $date = new \DateTime('2015-03-25');
        $formation->setPublishedAt($date);

        $expected = '25/03/2015';
        $this->assertEquals($expected, $formation->getPublishedAtString());
    }
}