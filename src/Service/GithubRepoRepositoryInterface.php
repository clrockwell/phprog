<?php

namespace App\Service;

use App\Entity\GithubRepo;

interface GithubRepoRepositoryInterface
{
    public function getTopRepos(): array;
}
