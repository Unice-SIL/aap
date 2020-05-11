<?php


namespace App\Controller;


use App\Entity\ProjectFormLayout;
use App\Entity\ProjectFormWidget;
use App\Manager\Project\ProjectManagerInterface;
use App\Manager\ProjectFormWidget\ProjectFormWidgetManagerInterface;
use App\Widget\WidgetManager;
use Doctrine\ORM\EntityManagerInterface;
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
        ProjectFormWidget $projectFormWidget,
        Request $request,
        ProjectFormWidgetManagerInterface $projectFormWidgetManager,
        TranslatorInterface $translator,
        RouterInterface $router,
        ProjectManagerInterface $projectManager,
        WidgetManager $widgetManager

    ): Response
    {
        $widget = $projectFormWidget->getWidget();

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

                $project = $projectManager->create($projectFormWidget->getProjectFormLayout()->getCallOfProject());
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
     * @Route("/{id}", name="trash_toggle", methods={"POST"})
     * @param Request $request
     * @param ProjectFormWidget $projectFormWidget
     * @return Response
     */
    public function trashToggle(Request $request, ProjectFormWidget $projectFormWidget): Response
    {
        if ($this->isCsrfTokenValid('trash_toggle'.$projectFormWidget->getId(), $request->request->get('_token'))) {

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
}