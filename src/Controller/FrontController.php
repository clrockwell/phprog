<?php

namespace App\Controller;

use App\Repository\GithubRepoRepository;
use App\Repository\GithubRepoUpdateLogRepository;
use App\Service\GithubRepoService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

class FrontController extends AbstractController
{
    public function __construct(
        private readonly GithubRepoService $githubRepoService,
        private readonly GithubRepoUpdateLogRepository $githubRepoUpdateLogRepository,
        private readonly GithubRepoRepository $githubRepoRepository
    ) {}

    #[Route('/', name: 'home')]
    public function index(): Response
    {
        // 30 because that's all we're retrieving :)
        $repos = $this->githubRepoService->getTopRepos(30);
        $last_update = $this->githubRepoUpdateLogRepository
            ->createQueryBuilder('l')
            ->orderBy('l.id', 'DESC')
            ->setMaxResults(1)
            ->select('l.updated_at')
            ->getQuery()
            ->getOneOrNullResult();

        return $this->render('front/index.html.twig', [
            'repos' => $repos,
            'last_updated' => $last_update,
        ]);
    }

    // For simplicity, this will also have the single component
    #[Route('/repository/{id}', name: 'repository_detail')]
    public function repositoryDetail(int $id): Response
    {
        $repository = $this->githubRepoRepository->find($id);
        if (empty($repository)) {
            throw new NotFoundHttpException('The repository does not exist');
        }

        return $this->render('front/detail.html.twig', [
            'repo' => $repository
        ]);
    }
}
