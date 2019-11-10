<?php

namespace App\Controller;

use App\Service\Pagination;
use App\Service\StatsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminDashboardController extends AbstractController
{
    /**
     * @Route("/admin/{page<\d+>?1}", name="admin_dashboard")
     */
    public function index(Pagination $pagination, $page, StatsService $statsService)
    {
        $stats = $statsService->getStats();

        $bestAds = $statsService->getBestAds();
        $worstAds = $statsService->getWorstAds();

        return $this->render('admin/dashboard/index.html.twig', [
            'stats' => $stats,
            'pagination' => $pagination,
            'bestAds' => $bestAds,
            'worstAds' => $worstAds
        ]);
    }
}
