<?php


namespace App\DataFixtures;


use Doctrine\Bundle\FixturesBundle\Fixture;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;

use Doctrine\Persistence\ObjectManager;


class ProgramFixtures extends Fixture implements DependentFixtureInterface

{

    public function load(ObjectManager $manager)

    {

    }


    public function getDependencies()

    {

        // Tu retournes ici toutes les classes de fixtures dont ProgramFixtures dépend

        return [

          ActorFixtures::class,

          CategoryFixtures::class,

        ];

    }



}
