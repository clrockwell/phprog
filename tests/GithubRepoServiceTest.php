<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\ORM\Tools\SchemaTool;

class GithubRepoServiceTest extends KernelTestCase
{
    private $entityManager;
    private $githubRepoRepository;

    public function setUp(): void
    {
        self::bootKernel();


        $this->entityManager = self::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $schemaTool = new SchemaTool($this->entityManager);
        $metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool->dropDatabase();
        if (!empty($metadata)) {
            $schemaTool->createSchema($metadata);
        }

        $this->githubRepoRepository = $this->entityManager->getRepository(GithubRepoRepository::class);
    }

    public function testGetTopRepos()
    {
        $githubRepoService = new GithubRepoService($this->githubRepoRepository);
        $result = $githubRepoService->getTopRepos();
        $this->assertEquals([], $result);
    }
}
