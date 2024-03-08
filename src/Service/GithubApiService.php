<?php

namespace App\Service;

use App\Entity\GithubRepoUpdateLog;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GithubApiService
{
    private $baseUrl = 'https://api.github.com';

    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly EntityManagerInterface $entityManager
    ) {}

    /**
     * @inheritDoc
     */
    public function search(): array {
        $response = $this->client->request('GET', "{$this->baseUrl}/search/repositories?q=language:php&sort=stars&order=desc");
        if ($response->getStatusCode() !== 200) {
            $message = 'Failed to fetch data from Github. ';
            if ($response->getStatusCode() === 403) {
                $message .= 'This is likely due to throttling imposed by GitHub, ';
                $message .= 'which is 10 requests per minute when not using ';
                $message .= 'an API key';
            }
            throw new \Exception($message);
        }

        /**
         * Chris Rockwell - TODO
         * This code would not live here, we'd have a separate service for the GithubRepoUpdateRepository.
         *
         * It is possible, possibly preferred, that we have the Observer pattern implemented (which I believe
         * Symfony does out of the box) so that we can subscribe to API call events.  This would allow us
         * to hand off this logging to a background service, returning more quickly.
         */
        $log = new GithubRepoUpdateLog();
        $log->setUpdatedAt(new \DateTimeImmutable());
        $this->entityManager->persist($log);
        $this->entityManager->flush();

        return $response->toArray();
    }
}
