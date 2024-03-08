<?php
namespace App\Service;

use App\Entity\GithubRepo;
use App\Repository\GithubRepoRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

class GithubRepoService implements GithubRepoServiceInterface
{
    public function __construct(
        private readonly GithubRepoRepository   $githubRepoRepository,
        private readonly EntityManagerInterface $entityManager
    ) {}

    /**
     * @inheritDoc
     */
    public function getTopRepos($limit = 10): array
    {
        return $this->githubRepoRepository->findBy([], ['stargazers_count' => 'DESC'], $limit);
    }

    /**
     * I want to note that this create function does not sit well with me and, given more time to research,
     * I would find a better way to handle this, the Symfony way.  This shows a lack of familiarity with the
     * Symfony and Doctrine way of doing things.
     *
     * In particular, entity hyrdration.
     *
     * @param array $data
     * @return boolean
     */
    public function create(array $data, $do_flush = true): bool
    {
        $this->validate($data);

        $entity = new GithubRepo();
        $entity->setRepositoryId($data['repository_id']);
        $entity->setName($data['name']);
        $entity->setHtmlUrl($data['html_url']);
        $entity->setCreatedAt(new DateTimeImmutable($data['created_at']));
        $entity->setPushedAt(new DateTimeImmutable($data['pushed_at']));
        if (isset($data['description'])) {
            $entity->setDescription($data['description']);
        }
        $entity->setStargazersCount($data['stargazers_count']);

        $this->entityManager->persist($entity);

        if (!$do_flush) {
            return true;
        }

        $this->entityManager->flush();

        // Probably a try/catch, but I need to read up on exactly how doctine entity manager works before knowing how best to handle this.
        if ($entity->getId() === null) {
            return false;
        }

        return true;
    }

    /**
     * Chris Rockwell - TODO
     * Given more time we'd have...
     * - A different return type that is a class with a success and errors property
     * - Consideration of using a batch process or queue to handle this, especially if we were doing more than the 30 records (which is default returned by github)
     */

    public function createMultiple(array $data): bool
    {
        foreach ($data as $datum) {
            $this->create($datum, false);
        }
        $this->entityManager->flush();

        return true;
    }

    /**
     * @inheritDoc
     * Chris Rockwell - TODO
     * Missing proper error handling and validation.
     */
    public function upsert(array $data): bool
    {
        $new = [];
        foreach ($data as $datum) {
            $existing = $this->githubRepoRepository->findOneBy(['repository_id' => $datum['id']]);
            // Likely a better way to hydrate an existing entity from an array.
            if ($existing) {
                $existing->setName($datum['name']);
                $existing->setHtmlUrl($datum['html_url']);
                $existing->setPushedAt(new DateTimeImmutable($datum['pushed_at']));
                $existing->setDescription($datum['description']);
                $existing->setStargazersCount($datum['stargazers_count']);
                $this->entityManager->persist($existing);
            } else {
                $new[] = $datum;
            }
        }
        if (count($new) > 0) {
            // We need to map the id in the data to the repository_id
            array_walk($new, function(&$val, $key) {
                $val['repository_id'] = $val['id'];
                unset($val['id']);
            });
            $this->createMultiple($new);
        }

        $this->entityManager->flush();

        return true;
    }

    /**
     * Chris Rockwell - TODO
     * Not satisfied with this implementation; seems there would be a more universal way to validate against the entity.
     *
     * Also, I refactored this to move it, the caller shouldn't be responsible for try/catch - this should just
     * return boolean
     */
    private function validate(array $data) : void {
        $classMetadata = $this->entityManager->getClassMetadata(GithubRepo::class);
        $requiredFields = array_filter($classMetadata->fieldMappings, function ($fieldMapping) {
            // array access is getting deprecation warnings
            return $fieldMapping['id'] !== true && !$fieldMapping['nullable'];
        });
        foreach ($requiredFields as $field => $mapping) {
            if (!isset($data[$field])) {
                throw new \InvalidArgumentException(sprintf('Field "%s" is required', $field));
            }
        }
    }
}
