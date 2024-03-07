<?php
namespace App\Service;

interface GithubRepoServiceInterface
{
    /**
     * @return GithubRepo[]
     */
    public function getTopRepos(): array;
}
