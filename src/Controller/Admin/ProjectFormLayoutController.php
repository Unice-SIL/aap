<?php


namespace App\Controller\Admin;

use App\Entity\ProjectFormLayout;
use App\Form\ProjectFormLayout\ProjectFormLayoutInformationType;
use App\Manager\CallOfProject\CallOfProjectManagerInterface;
use App\Manager\Project\ProjectManagerInterface;
use App\Manager\ProjectFormLayout\ProjectFormLayoutManagerInterface;
use App\Widget\WidgetManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ProjectFormLayoutController
 * @package App\Controller\Admin
 * @Route("/project_form_layout", name="project_form_layout.")
 */
class ProjectFormLayoutController extends AbstractController
{
    /**
     * @Route("", name="index", methods={"GET"})
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function index(EntityManagerInterface $entityManager)
    {
        return $this->render('project_form_layout/index.html.twig', [
            'templates' => $entityManager->getRepository(ProjectFormLayout::class)->findByIsTemplate(true),
        ]);
    }

    /**
     * @Route("/new", name="new", methods={"GET", "POST"})
     * @param ProjectFormLayoutManagerInterface $projectFormLayoutManager
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param TranslatorInterface $translator
     */
    public function new(
        ProjectFormLayoutManagerInterface $projectFormLayoutManager,
        Request $request,
        EntityManagerInterface $em,
        TranslatorInterface $translator
    )
    {
        $projectFormLayout = $projectFormLayoutManager->create();
        $projectFormLayout->setIsTemplate(true);
        $form = $this->createForm(ProjectFormLayoutInformationType::class, $projectFormLayout);
        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {

            $this->addFlash('success', $translator->trans('app.flash_message.create_success', [
                '%item%' => $projectFormLayout->getName()
            ]));

            $em->persist($projectFormLayout);
            $em->flush();

            return $this->redirectToRoute('app.admin.project_form_layout.configure', ['id' => $projectFormLayout->getId()]);
        }

        return $this->render('project_form_layout/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/configure", name="configure", methods={"GET", "POST"})
     * @param ProjectFormLayout $projectFormLayout
     * @param ProjectManagerInterface $projectManager
     * @param CallOfProjectManagerInterface $callOfProjectManager
     * @param WidgetManager $widgetManager
     * @return Response
     * @throws \Exception
     */
    public function configure(
        ProjectFormLayout $projectFormLayout,
        ProjectManagerInterface $projectManager,
        CallOfProjectManagerInterface $callOfProjectManager,
        WidgetManager $widgetManager
    )
    {

        $callOfProject = $callOfProjectManager->create();
        $callOfProject->addProjectFormLayout($projectFormLayout);
        $project = $projectManager->create($callOfProject);
        $dynamicForm = $widgetManager->getDynamicForm($project, ['allWidgets' => false]);

        return $this->render('project_form_layout/configure.html.twig', [
            'project_form_layout' => $projectFormLayout,
            'widget_manager' => $widgetManager,
            'dynamic_form_html' => $widgetManager->renderDynamicFormHtml(
                $dynamicForm,
                'partial/widget/_dynamic_form_demo.html.twig'
            ),
        ]);
    }

    /**
     * @Route("/{id}/show", name="show", methods={"GET"})
     * @param ProjectFormLayout $projectFormLayout
     * @param CallOfProjectManagerInterface $callOfProjectManager
     * @param ProjectManagerInterface $projectManager
     * @param WidgetManager $widgetManager
     * @return Response
     */
    public function show(
        ProjectFormLayout $projectFormLayout,
        CallOfProjectManagerInterface $callOfProjectManager,
        ProjectManagerInterface $projectManager,
        WidgetManager $widgetManager
    )
    {
        $callOfProject = $callOfProjectManager->create();
        $callOfProject->addProjectFormLayout($projectFormLayout);
        $project = $projectManager->create($callOfProject);
        $dynamicForm = $widgetManager->getDynamicForm($project, ['allWidgets' => false]);

        return $this->render('project_form_layout/show.html.twig', [
            'project_form_layout' => $projectFormLayout,
            'dynamic_form_html' => $widgetManager->renderDynamicFormHtml(
                $dynamicForm,
                'partial/widget/_dynamic_form.html.twig'
            ),
        ]);
    }
}