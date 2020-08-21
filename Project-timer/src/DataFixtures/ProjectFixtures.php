<?php


namespace App\DataFixtures;

use App\Entity\Project;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as Faker;

class ProjectFixtures
{

    public function load(ObjectManager $manager)
    {
        $faker = Faker::create();

        for ($i = 0; $i < 5; $i++) {
            $project = new Project();
            $project->setName($faker->name);
            $manager->persist($project);
        }

        $manager->flush();
    }
}