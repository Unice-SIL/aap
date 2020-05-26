<?php


namespace App\Controller\Front;


use App\Entity\CallOfProject;
use App\Entity\ProjectFormLayout;
use App\Manager\CallOfProject\CallOfProjectManagerInterface;
use App\Manager\Project\ProjectManagerInterface;
use App\Manager\ProjectFormLayout\ProjectFormLayoutManagerInterface;
use App\Manager\ProjectFormWidget\ProjectFormWidgetManagerInterface;
use App\Repository\ProjectFormLayoutRepository;
use App\Security\CallOfProjectVoter;
use App\Security\UserVoter;
use App\Widget\WidgetManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
     * @param CallOfProjectManagerInterface $callOfProjectManager
     * @param ProjectFormLayoutManagerInterface $projectFormLayoutManager
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
        if (!$projectFormLayout->getCallOfProject() instanceof  CallOfProject) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');
        } else {
            $this->denyAccessUnlessGranted(CallOfProjectVoter::ADMIN, $projectFormLayout->getCallOfProject());
        }

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


                $callOfProject = $projectFormLayout->getCallOfProject();
                if (!$callOfProject instanceof CallOfProject) {
                    $callOfProject = new CallOfProject();
                    $callOfProject->addProjectFormLayout($projectFormLayout);
                }

                $project = $projectManager->create($callOfProject);
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

    /**
     * @Route("/list-all-templates-select-2", name="list_all_templates_select_2", methods={"GET"})
     * @param Request $request
     * @param ProjectFormLayoutRepository $projectFormLayoutRepository
     * @return JsonResponse
     */
    public function listAllTemplatesSelect2(Request $request, ProjectFormLayoutRepository $projectFormLayoutRepository)
    {

        $this->denyAccessUnlessGranted(UserVoter::ADMIN_ONE_ORGANIZING_CENTER_AT_LEAST, $this->getUser());

        $query = $request->query->get('q');

        $projectFormLayouts = array_map(function ($projectFormLayout) {
            return [
                'id' => $projectFormLayout->getId(),
                'text' => $projectFormLayout->getName()
            ];
        }, $projectFormLayoutRepository->getTemplateByNameLikeQuery($query));

        return $this->json($projectFormLayouts);
    }
}