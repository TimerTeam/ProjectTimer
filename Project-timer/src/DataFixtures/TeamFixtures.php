<?php


namespace App\DataFixtures;

use App\Entity\Team;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as Faker;


class TeamFixtures extends Fixture
{
    
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    
    public function load(ObjectManager $manager)
    {
        $faker = Faker::create();
        
        $userList = $this->userRepository->findAll();

        for ($i = 0; $i < 5; $i++) {
            $team = new Team();
            
          //  $userIndex = $faker->numberBetween(1, count($userList)-1);
            $team->setName($faker->name);
            $team->setTeamAdmin($faker->numberBetween(0, 100));
          //  $team->addUser($userList[$userIndex]);

            $manager->persist($team);
        }

        $manager->flush();
    }
}