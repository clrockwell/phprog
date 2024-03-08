<?php
namespace App\Service;

use App\Entity\GithubRepo;

interface GithubRepoServiceInterface
{
    /**
     * @return GithubRepo[]
     */
    public function getTopRepos($limit = 10): array;

    public function create(array $data, $do_flush = true): bool;

    public function createMultiple(array $data): bool;

    /**
     * TODO Doctrine probably has something to handle this already.
     * @param array $data
     * @return bool
     */
    public function upsert(array $data): bool;
}
