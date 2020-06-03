<?php


namespace App\Controller\Front;


use App\Repository\ReportRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ReportController
 * @package App\Controller\Front
 * @Route("/report", name="report.")
 */
class ReportController extends AbstractController
{
    /**
     * @param ReportRepository $reportRepository
     * @return Response
     * @Route(name="index", methods={"GET"})
     */
    public function index(ReportRepository $reportRepository)
    {
        return $this->render('report/index.html.twig', [
            'reports' => $reportRepository->findByReporter($this->getUser()),
        ]);
    }
}