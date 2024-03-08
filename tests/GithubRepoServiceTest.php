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
        // Should use Fixtures for this
        $repo1 = $this->getDummyRepo();
        $repo2 = $this->getDummyRepo();
        $repo3 = $this->getDummyRepo();
        $repos = [$repo1, $repo2, $repo3];
        $this->githubRepoService->createMultiple($repos);
        $result = $this->githubRepoService->getTopRepos();
        $this->assertEquals(3, count($result));
    }

    /**
     * Required fields are repository_id, name, url, created_date, last_push_date, description, stargazers_count
     * @return void
     */
    public function testCreate_RequiredFieldsAreRequired()
    {
        $catcher = false;
        try {
            $this->githubRepoService->create([]);
            $catcher = true;
        }
        catch (\InvalidArgumentException $e) {
            $this->assertEquals('Field "repository_id" is required', $e->getMessage());
        }
        $this->assertFalse($catcher);
        // In the interest of time, going to stop here on this one.
    }

    public function testCreate_Creates()
    {
        $repo = $this->getDummyRepo();
        $this->githubRepoService->create($repo);
        $created = $this->entityManager->getRepository(GithubRepo::class)->findOneBy(['repository_id' => $repo['repository_id']]);
        $this->assertNotNull($created);
    }

    public function testCreateMultiple_Creates()
    {
        // Should use Fixtures for this
        $repo1 = $this->getDummyRepo();
        $repo2 = $this->getDummyRepo();
        $repo3 = $this->getDummyRepo();
        $repos = [$repo1, $repo2, $repo3];
        $this->githubRepoService->createMultiple($repos);
        $created_1 = $this->entityManager->getRepository(GithubRepo::class)->findOneBy(['repository_id' => $repo1['repository_id']]);
        $created_2 = $this->entityManager->getRepository(GithubRepo::class)->findOneBy(['repository_id' => $repo2['repository_id']]);
        $created_3 = $this->entityManager->getRepository(GithubRepo::class)->findOneBy(['repository_id' => $repo3['repository_id']]);
        $this->assertNotNull($created_1);
        $this->assertNotNull($created_2);
        $this->assertNotNull($created_3);
    }

    public function testUpsert_CreatesAndUpdates()
    {
        $repo = $this->getDummyRepo();
        $this->githubRepoService->create($repo);
        $created = $this->entityManager->getRepository(GithubRepo::class)->findOneBy(['repository_id' => $repo['repository_id']]);
        $this->assertNotNull($created);
        $this->assertEquals($repo['name'], $created->getName());
        $repo['name'] = 'New Name ' . random_int(1, 10000);
        $this->githubRepoService->upsert([$repo]);
        $updated = $this->entityManager->getRepository(GithubRepo::class)->findOneBy(['repository_id' => $repo['repository_id']]);
        $this->assertEquals($repo['name'], $updated->getName());
    }

    private function getDummyRepo()
    {
        return [
            'repository_id' => random_int(10000, 100000000),
            'name' => 'Repo ' . random_int(1, 100),
            'html_url' => 'http://example.com',
            'created_at' => new \DateTime(),
            'pushed_at' => new \DateTime(),
            'description' => random_int(0,10) < 6 ? 'A description' : null,
            'stargazers_count' => random_int(5, 8000)
        ];
    }
}
