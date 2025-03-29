<?php

namespace App\tests\Entity;

use App\Entity\Formation;
use PHPUnit\Framework\TestCase;

/**
 * @author arthur poncin
 */
class FormationTest extends TestCase
{
    public function testGetPublishedAtStringWithTime()
    {
        $formation = new Formation();
        $date = new \DateTime('2020-01-04 17:00:12');
        $formation->setPublishedAt($date);

        $expected = '04/01/2020';
        $this->assertEquals($expected, $formation->getPublishedAtString());
    }
    
    public function testGetPublishedAtStringWithMarchDate()
    {
        $formation = new Formation();
        $date = new \DateTime('2024-03-25');
        $formation->setPublishedAt($date);

        $expected = '25/03/2024';
        $this->assertEquals($expected, $formation->getPublishedAtString());
    }
    
    public function testGetPublishedAtStringWithNullDate()
    {
        $formation = new Formation();
        $expected = '';
        $this->assertEquals($expected, $formation->getPublishedAtString());
    }
    
    public function testGetPublishedAtStringWithLowerYearDate()
    {
        $formation = new Formation();
        $date = new \DateTime('2015-03-25');
        $formation->setPublishedAt($date);

        $expected = '25/03/2015';
        $this->assertEquals($expected, $formation->getPublishedAtString());
    }
}