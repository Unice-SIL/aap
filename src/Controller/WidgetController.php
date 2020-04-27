<?php


namespace App\Controller;


use App\Entity\ProjectFormLayout;
use App\Entity\ProjectFormWidget;
use App\Manager\ProjectFormWidget\ProjectFormWidgetManagerInterface;
use App\Widget\WidgetManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
     * @Route("/{id}/get-widget-form", name="get_widget_form", methods={"GET"})
     * @param ProjectFormLayout $projectFormLayout
     * @param WidgetManager $widgetManager
     * @param Request $request
     * @param RouterInterface $router
     * @return Response
     */
    public function getWidgetForm(
        ProjectFormLayout $projectFormLayout,
        WidgetManager $widgetManager,
        Request $request,
        RouterInterface $router
    ): Response
    {
        $widgetName = $request->query->get('widgetName');

        if (!$widget = $widgetManager->getWidget($widgetName)) {
            return $this->json(['success' => false]);
        }

        $form = $this->createForm($widget->getFormType(), $widget, [
            'action' => $router->generate('app.call_of_project.form', [
                'id' => $projectFormLayout->getCallOfProject()->getId(),
                'widgetName' => $widgetName
            ])
        ]);

        return $this->render($widget->getTemplate(), [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/get-filled-form", name="get_filled_widget_form", methods={"GET"})
     * @param ProjectFormWidget $projectFormWidget
     * @param RouterInterface $router
     * @return Response
     */
    public function getFilledWidgetForm(
        ProjectFormWidget $projectFormWidget,
        RouterInterface $router
    ): Response
    {

        $widget = $projectFormWidget->getWidget();

        $form = $this->createForm($widget->getFormType(), $widget, [
            'action' => $router->generate('app.widget.edit', [
                'id' => $projectFormWidget->getId(),
            ])
        ]);

        return $this->render($widget->getTemplate(), [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit", methods={"POST"})
     * @param ProjectFormWidget $projectFormWidget
     * @param Request $request
     * @param ProjectFormWidgetManagerInterface $projectFormWidgetManager
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function edit(
        ProjectFormWidget $projectFormWidget,
        Request $request,
        ProjectFormWidgetManagerInterface $projectFormWidgetManager,
        TranslatorInterface $translator

    ): Response
    {

        if (!$projectFormWidget->isActive()) {
            throw $this->createAccessDeniedException($translator->trans('app.project_form_widget.edition_on_trash_widget_denied'));
        }
        $widget = $projectFormWidget->getWidget();

        $form = $this->createForm($widget->getFormType(), $widget);
        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {

            $this->addFlash('success', $translator->trans('app.flash_message.edit_success', [
                '%item%' => $translator->trans('app.project_form_widget.humanize')
            ]));

            $projectFormWidget->setWidget($widget);

            $projectFormWidgetManager->update($projectFormWidget);
        }

        return $this->redirectToRoute('app.call_of_project.form', [
            'id' => $projectFormWidget->getProjectFormLayout()->getCallOfProject()->getId(),
        ]);
    }

    /**
     * Route("/{id}", name="delete", methods={"DELETE"})
     * @param Request $request
     * @param ProjectFormWidget $projectFormWidget
     * @return Response
     */
    /*public function delete(Request $request, ProjectFormWidget $projectFormWidget): Response
    {
        if ($this->isCsrfTokenValid('delete'.$projectFormWidget->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($projectFormWidget);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app.call_of_project.form', [
            'id' => $projectFormWidget->getProjectFormLayout()->getCallOfProject()->getId()
        ]);
    }*/

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
}