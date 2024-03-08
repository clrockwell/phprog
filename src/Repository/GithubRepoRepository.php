<?php

namespace App\Repository;

use App\Entity\GithubRepo;
use App\Service\GithubRepoRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GithubRepo>
 *
 * @method GithubRepo|null find($id, $lockMode = null, $lockVersion = null)
 * @method GithubRepo|null findOneBy(array $criteria, array $orderBy = null)
 * @method GithubRepo[]    findAll()
 * @method GithubRepo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GithubRepoRepository extends ServiceEntityRepository implements GithubRepoRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GithubRepo::class);
    }

    public function getTopRepos(): array {
        // TODO limit should be configurable
        return $this->findBy([], ['stargazers_count' => 'DESC'], 10);
    }
}
