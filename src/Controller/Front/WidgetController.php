<?php


namespace App\Controller\Front;


use App\Entity\CallOfProject;
use App\Entity\ProjectFormLayout;
use App\Entity\ProjectFormWidget;
use App\Manager\Project\ProjectManagerInterface;
use App\Manager\ProjectFormWidget\ProjectFormWidgetManagerInterface;
use App\Security\CallOfProjectVoter;
use App\Widget\WidgetManager;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class WidgetController
 * @package App\Controller
 * @Route("/widget", name="widget.")
 */
class WidgetController extends AbstractController
{
    /**
     * @Route("/{id}/edit", name="edit", methods={"GET","POST"})
     * @param ProjectFormWidget $projectFormWidget
     * @param Request $request
     * @param ProjectFormWidgetManagerInterface $projectFormWidgetManager
     * @param TranslatorInterface $translator
     * @param RouterInterface $router
     * @param ProjectManagerInterface $projectManager
     * @param WidgetManager $widgetManager
     * @return Response
     */
    public function edit(
        ProjectFormWidget                 $projectFormWidget,
        Request                           $request,
        ProjectFormWidgetManagerInterface $projectFormWidgetManager,
        TranslatorInterface               $translator,
        RouterInterface                   $router,
        ProjectManagerInterface           $projectManager,
        WidgetManager                     $widgetManager

    ): Response
    {
        if (!$projectFormWidget->getProjectFormLayout()->getCallOfProject() instanceof CallOfProject) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');
        } else {
            $this->denyAccessUnlessGranted(CallOfProjectVoter::ADMIN, $projectFormWidget->getProjectFormLayout()->getCallOfProject());
        }

        $widget = $widgetManager->extractWidget($projectFormWidget);

        $form = $this->createForm($widget->getFormType(), $widget, [
            'action' => $router->generate('app.widget.edit', [
                'id' => $projectFormWidget->getId(),
            ])
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            if (!$projectFormWidget->isActive()) {
                throw $this->createAccessDeniedException($translator->trans('app.project_form_widget.edition_on_trash_widget_denied'));
            }

            if ($form->isValid()) {

                /*$this->addFlash('success', $translator->trans('app.flash_message.edit_success', [
                    '%item%' => $translator->trans('app.project_form_widget.humanize')
                ]));*/

                $projectFormWidget->setWidget($widget);

                $projectFormWidgetManager->update($projectFormWidget);

                $callOfProject = $projectFormWidget->getProjectFormLayout()->getCallOfProject();
                if (!$callOfProject instanceof CallOfProject) {
                    $callOfProject = new CallOfProject();
                    $callOfProject->addProjectFormLayout($projectFormWidget->getProjectFormLayout());
                }

                $project = $projectManager->create($callOfProject);
                $dynamicForm = $widgetManager->getDynamicForm($project, ['allWidgets' => true]);
                return $this->render('partial/widget/_project_form_widget_card_edition.html.twig', [
                    'project_form_widget' => $projectFormWidget,
                    'form' => $dynamicForm->createView(),
                    'widget_edit' => true
                ]);
            }

        }


        return $this->render($widget->getTemplate(), [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/trash_toggle", name="trash_toggle", methods={"POST"})
     * @param Request $request
     * @param ProjectFormWidget $projectFormWidget
     * @return Response
     */
    public function trashToggle(Request $request, ProjectFormWidget $projectFormWidget): Response
    {
        $this->denyAccessUnlessGranted(
            CallOfProjectVoter::ADMIN, $projectFormWidget->getProjectFormLayout()->getCallOfProject()
        );

        if ($this->isCsrfTokenValid('trash_toggle' . $projectFormWidget->getId(), $request->request->get('_token'))) {

            $projectFormWidget->isActiveToggle();

            $this->getDoctrine()->getManager()->flush();
        }

        return $this->redirectToRoute('app.call_of_project.form', [
            'id' => $projectFormWidget->getProjectFormLayout()->getCallOfProject()->getId()
        ]);
    }

    /**
     * @Route("/{id}/update-position", name="update_position", methods={"POST"})
     * @param ProjectFormWidget $projectFormWidget
     * @param Request $request
     * @return JsonResponse
     */
    public function updatePosition(ProjectFormWidget $projectFormWidget, Request $request, EntityManagerInterface $em)
    {

        if (!$projectFormWidget->getProjectFormLayout()->getCallOfProject() instanceof CallOfProject) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');
        } else {
            $this->denyAccessUnlessGranted(CallOfProjectVoter::ADMIN, $projectFormWidget->getProjectFormLayout()->getCallOfProject());
        }

        $position = $request->request->getInt('position');

        if ($position === null) {
            return $this->json(['success' => false]);
        }

        $projectFormLayout = $projectFormWidget->getProjectFormLayout();

        foreach ($projectFormLayout->getProjectFormWidgets() as $projectFormWidgetLoop) {

            if ($position < $projectFormWidget->getPosition()) {
                if (
                    $projectFormWidgetLoop->getPosition() < $position
                    or $projectFormWidgetLoop->getPosition() >= $projectFormWidget->getPosition()
                ) {
                    continue;
                }

                $projectFormWidgetLoop->setPosition($projectFormWidgetLoop->getPosition() + 1);
            }

            if ($position > $projectFormWidget->getPosition()) {
                if (
                    $projectFormWidgetLoop->getPosition() > $position
                    or $projectFormWidgetLoop->getPosition() <= $projectFormWidget->getPosition()
                ) {
                    continue;
                }
                $projectFormWidgetLoop->setPosition($projectFormWidgetLoop->getPosition() - 1);
            }

        }

        $projectFormWidget->setPosition($position);

        $em->flush();

        return $this->json(['success' => true]);
    }

    /**
     * @Route("/{id}/edit-title", name="edit_title", methods={"GET","POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function editTitle($id, EntityManagerInterface $entityManager, Request $request
    ): Response
    {
        if ($request->isMethod("POST")) {
            $data = $request->request->get('project_form_layout');
            $title =  $data['title'];
            /** @var ProjectFormLayout $projectFormLayout */
            $projectFormLayout =  $entityManager->getRepository(ProjectFormLayout::class)->findOneBy(['id' => $id]);
            $projectFormLayout->setTitle($title);
            $entityManager->flush();
            return new JsonResponse([
                'statut' => true,
                'newLabel' => $title
            ]);
        }
        new JsonResponse([
            'statut' => false
        ]);
    }
}
