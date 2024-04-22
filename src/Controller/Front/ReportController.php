<?php


namespace App\Controller\Front;


use App\Entity\Report;
use App\Form\Report\DeadlineType;
use App\Form\Report\ReportType;
use App\Manager\Report\ReportManagerInterface;
use App\Repository\ReportRepository;
use App\Security\CallOfProjectVoter;
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
     * @Route("/{id}/show", name="show", methods={"GET", "POST"})
     * @IsGranted(App\Security\ReportVoter::SHOW, subject="report")
     * @param Report $report
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function show(Report $report, Request $request, TranslatorInterface $translator)
    {
        $context = $request->query->get('context');
        if ($context === 'call_of_project') {
            $layout = 'call_of_project/layout.html.twig';
        }

        $deadlineForm = $this->createForm(DeadlineType::class, $report);
        $deadlineForm->handleRequest($request);

        if ($deadlineForm->isSubmitted() and $deadlineForm->isValid()) {

            $this->denyAccessUnlessGranted(CallOfProjectVoter::EDIT, $report->getProject()->getCallOfProject());

            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', $translator->trans('app.flash_message.edit_success', ['%item%' => $report->getName()]));

            $routeParams = ['id' => $report->getId()];
            if ($context === 'call_of_project') {
                $routeParams['context'] = $context;
            }
            return $this->redirectToRoute('app.report.show', $routeParams);
        }

        return $this->render('report/show.html.twig', [
            'report' => $report,
            'layout' => $layout ?? null,
            'call_of_project' => $report->getProject()->getCallOfProject(),
            'context' => $context,
            'deadline_form' => $deadlineForm->createView()
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit", methods={"GET", "POST"})
     * @IsGranted(App\Security\ReportVoter::EDIT, subject="report")
     * @param Request $request
     * @param Report $report
     * @param ReportManagerInterface $reportManager
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function edit(Request $request, Report $report, ReportManagerInterface $reportManager, TranslatorInterface $translator): Response
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

    /**
     * @param Request $request
     * @param Report $report
     * @param TranslatorInterface $translator
     */
    private function delete(Request $request, Report $report, TranslatorInterface $translator)
    {
        if ($this->isCsrfTokenValid('delete'.$report->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($report);
            $entityManager->flush();
            $this->addFlash('success', $translator->trans('app.flash_message.report_delete_success', ['%reporter%' => $report->getReporter()->getUsername()]));
        }
    }

    /**
     * @Route("/{id}/project", name="delete_from_project", methods={"DELETE"})
     * @param Request $request
     * @param Report $report
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function deleteFromProject(Request $request, Report $report, TranslatorInterface $translator): Response
    {
        $this->delete($request, $report, $translator);
        return $this->redirectToRoute('app.project.show', [
            'id' => $report->getProject()->getId(),
            'context' => 'call_of_project'
        ]);
    }

    /**
     * @Route("/{id}/call_of_project", name="delete_from_cop", methods={"DELETE"})
     * @param Request $request
     * @param Report $report
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function deleteFromCOP(Request $request, Report $report, TranslatorInterface $translator): Response
    {
        $this->delete($request, $report, $translator);
        return $this->redirectToRoute('app.call_of_project.reports', [
            'id' => $report->getProject()->getCallOfProject()->getId(),
            'context' => 'call_of_project'
        ]);
    }
}