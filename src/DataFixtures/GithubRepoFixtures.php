<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class GithubRepoFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // How about 17?
        for ($i = 0; $i < 17; $i++) {
            $githubRepo = new GithubRepo();
            $githubRepo->setName('Repo ' . $i);
            $githubRepo->setStargazersCount(random_int(0, 1000));
            $manager->persist($githubRepo);
        }
        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}
