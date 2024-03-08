<?php

namespace App\Controller;

use App\Repository\GithubRepoUpdateLogRepository;
use App\Service\GithubRepoService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\GithubApiService;

class GithubRepoController extends AbstractController
{
    public function __construct(
        private readonly GithubApiService $githubApi,
        private readonly GithubRepoUpdateLogRepository $githubRepoUpdateLogRepository,
        private readonly GithubRepoService $githubRepoService
    ) {}

    // This functionality would be either in a service or we'd just be calling the GithubApiService directly.
    public function search(): array
    {
        try {
            $data = $this->githubApi->search();
            // TODO - Github response looks like ['total_count', 'incomplete_results', 'items']
            //    In real-world we'd want to understand what incomplete_results means and deal with that and total_count = 0 appropriately.
            if (array_key_exists('items', $data) && count($data['items']) > 0) {
                return $data['items'];
            }

            return [];
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }
    }

    #[Route('/github/refresh', name: 'github_refresh')]
    public function refreshRedirect(Request $request, SessionInterface $session): RedirectResponse
    {
        $message = 'The results have been refreshed.';
        $isOk = true;
        try {
            $this->refresh();
        }
        catch (\Exception $exception) {
            $message = $exception->getMessage();
            $isOk = false;
        }

        $session->getFlashBag()->add($isOk ? 'success' : 'danger', $message);

        return new RedirectResponse('/');
    }

    public function refresh(): bool
    {
        if ($this->githubRepoUpdateLogRepository->countPotentialThrottles() > 9) {
            throw new ThrottleException(429, 'Github may throttle this request, please wait.');
        }

        // In the interest of time, the database will be refreshed now.
        // In real-world scenarios we'd do some sort of asynchronous processing to load
        // the database whenever we're dealing with large datasets.
        // In this scenario, we just call search and load it up.
        $results = $this->search();

        if (empty($results)) {
            throw new NoResultsException(204, 'No results returned from Github');
             // 204 doesn't seem right here, as it's not this application with no content.
        }

        // Another note...we wouldn't want to send all this data.  We'd either be
        // mapping it to arrays or a class with a toArray() method.  But we'd also not be doing this here :)
        // Some error handling would need to be done also.
        $this->githubRepoService->upsert($results);

        return true;
    }
}

// Classes should not be in here, just checking out HttpKernel exceptions.
class ThrottleException extends HttpException {}

class NoResultsException extends HttpException {}