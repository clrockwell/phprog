<?php

namespace App\Repository;

use App\Entity\GithubRepoUpdateLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GithubRepoUpdateLog>
 *
 * @method GithubRepoUpdateLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method GithubRepoUpdateLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method GithubRepoUpdateLog[]    findAll()
 * @method GithubRepoUpdateLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GithubRepoUpdateLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GithubRepoUpdateLog::class);
    }

    /**
     * Github will throttle if we perform this query more than 10x in
     * 1 minute.  This is only here to show an example that we need to be
     * aware of limitations imposed by third parties and be prepared to deal
     * with them.
     */
    public function countPotentialThrottles() {
        $now = new \DateTimeImmutable();
        $oneMinuteAgo = $now->sub(new \DateInterval('PT1M'));
        $query = $this->createQueryBuilder('l')
            ->select('COUNT(l.id)')
            ->where('l.updated_at > :oneMinuteAgo')
            ->setParameter('oneMinuteAgo', $oneMinuteAgo)
            ->getQuery();

        return $query->getSingleScalarResult();
    }
}
