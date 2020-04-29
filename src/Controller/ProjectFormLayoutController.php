<?php


namespace App\Controller;


use App\Entity\ProjectFormLayout;
use App\Manager\Project\ProjectManagerInterface;
use App\Manager\ProjectFormWidget\ProjectFormWidgetManagerInterface;
use App\Widget\WidgetManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class ProjectFormLayoutController
 * @package App\Controller
 * @Route("/project-form-layout", name="project_form_layout.")
 */
class ProjectFormLayoutController extends AbstractController
{
    /**
     * @Route("/{id}/add-widget", name="add_widget", methods={"GET", "POST"})
     * @param ProjectFormLayout $projectFormLayout
     * @param WidgetManager $widgetManager
     * @param Request $request
     * @param RouterInterface $router
     * @param ProjectFormWidgetManagerInterface $projectFormWidgetManager
     * @param ProjectManagerInterface $projectManager
     * @return Response
     */
    public function addWidget(
        ProjectFormLayout $projectFormLayout,
        WidgetManager $widgetManager,
        Request $request,
        RouterInterface $router,
        ProjectFormWidgetManagerInterface $projectFormWidgetManager,
        ProjectManagerInterface $projectManager
    ): Response
    {

        $widgetName = $request->query->get('widgetName');

        if (!$widget = $widgetManager->getWidget($widgetName)) {
            return $this->json(['success' => false]);
        }

        $form = $this->createForm($widget->getFormType(), $widget, [
            'action' => $router->generate('app.project_form_layout.add_widget', [
                'id' => $projectFormLayout->getId(),
                'widgetName' => $widgetName
            ])
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            if ($form->isValid()) {

                $projectFormWidget = $projectFormWidgetManager->create();
                $projectFormWidget->setWidget($widget);
                $projectFormLayout->addProjectFormWidget($projectFormWidget);

                $projectFormWidgetManager->save($projectFormWidget);

                $project = $projectManager->create($projectFormWidget->getProjectFormLayout()->getCallOfProject());
                $dynamicForm = $widgetManager->getDynamicForm($project, ['allWidgets' => true]);
                return $this->render('partial/widget/_project_form_widget_card_edition.html.twig', [
                    'project_form_widget' => $projectFormWidget,
                    'form' => $dynamicForm->createView()
                ]);
            }

        }

        return $this->render($widget->getTemplate(), [
            'form' => $form->createView()
        ]);
    }
}