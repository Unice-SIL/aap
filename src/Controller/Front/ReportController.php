<?php


namespace App\Controller\Front;


use App\Entity\Report;
use App\Form\Report\ReportType;
use App\Manager\Report\ReportManagerInterface;
use App\Repository\ReportRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

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

    /**
     * @param Report $report
     * @param Request $request
     * @return Response
     * @Route("/{id}/show", name="show", methods={"GET"})
     * @IsGranted(App\Security\ReportVoter::SHOW, subject="report")
     */
    public function show(Report $report, Request $request)
    {
        $context = $request->query->get('context');
        if ($context === 'call_of_project') {
            $layout = 'call_of_project/layout.html.twig';
        }

        return $this->render('report/show.html.twig', [
            'report' => $report,
            'layout' => $layout ?? null,
            'call_of_project' => $report->getProject()->getCallOfProject(),
            'context' => $context
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit", methods={"GET", "POST"})
     * @param Request $request
     * @param Report $report
     * @param ReportManagerInterface $reportManager
     * @param TranslatorInterface $translator
     * @return Response
     * @IsGranted(App\Security\ReportVoter::EDIT, subject="report")
     */
    public function edit(Request $request, Report $report, ReportManagerInterface $reportManager, TranslatorInterface $translator)
    {
        $context = $request->query->get('context');
        if ($context === 'call_of_project') {
            $layout = 'call_of_project/layout.html.twig';
        }

        $form = $this->createForm(ReportType::class, $report);
        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {
            $reportManager->update($report);
            $this->addFlash('success', $translator->trans('app.flash_message.edit_success', ['%item%' => $report->getName()]));

            return $this->redirectToRoute('app.report.show', ['id' => $report->getId()]);
        }

        return $this->render('report/edit.html.twig', [
            'form' => $form->createView(),
            'report' => $report,
            'layout' => $layout ?? null,
            'call_of_project' => $report->getProject()->getCallOfProject(),
            'context' => $context
        ]);
    }
}