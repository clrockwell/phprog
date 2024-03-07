<?php
namespace App\Service;

use App\Repository\GithubRepoRepository;

class GithubRepoService implements GithubRepoServiceInterface
{
    public function __construct(private GithubRepoRepository $githubRepoRepository)
    {
        $this->githubRepoRepository = $githubRepoRepository;
    }

    /**
     * @inheritDoc
     */
    public function getTopRepos(): array
    {
        return $this->githubRepoRepository->findBy([], ['stargazers_count' => 'DESC'], 10);
    }
}
