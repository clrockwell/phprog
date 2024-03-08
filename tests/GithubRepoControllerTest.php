<?php

namespace App\Tests;

use App\Repository\GithubRepoUpdateLogRepository;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GithubRepoControllerTest extends WebTestCase
{
    private MockObject $githubRepoUpdateLogRepositoryMock;

    private KernelBrowser $client;

    public function setUp(): void
    {
        $this->client = static::createClient();

        // Create a mock of the GithubRepoUpdateLogRepository
        $this->githubRepoUpdateLogRepositoryMock = $this->getMockBuilder(GithubRepoUpdateLogRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testRefresh_ImposesThrottling(): void
    {
        $this->githubRepoUpdateLogRepositoryMock->method('countPotentialThrottles')
            ->willReturn(10);
        $this->client->getContainer()->set(GithubRepoUpdateLogRepository::class, $this->githubRepoUpdateLogRepositoryMock);
        $crawler = $this->client->request('GET', '/github/refresh');
        $this->assertResponseStatusCodeSame(429);
    }

    public function testRefresh_Returns200(): void {
        $this->githubRepoUpdateLogRepositoryMock->method('countPotentialThrottles')
            ->willReturn(9);
        $this->client->getContainer()->set(GithubRepoUpdateLogRepository::class, $this->githubRepoUpdateLogRepositoryMock);
        $crawler = $this->client->request('GET', '/github/refresh');
        $this->assertResponseStatusCodeSame(200);
    }
}
