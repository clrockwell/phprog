<?php

namespace App\Tests;

use App\Entity\GithubRepo;
use App\Service\GithubRepoService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\ORM\Tools\SchemaTool;

class GithubRepoServiceTest extends KernelTestCase
{
    private $entityManager;
    private $githubRepoService;

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
        $container = static::getContainer();
        $this->githubRepoService = $container->get(GithubRepoService::class);
    }

    public function testGetTopRepos()
    {
        $result = $this->githubRepoService->getTopRepos();
        $this->assertEquals([], $result);
    }
}
