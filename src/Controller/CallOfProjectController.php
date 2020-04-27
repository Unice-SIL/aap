<?php

namespace App\Controller;

use App\Entity\CallOfProject;
use App\Entity\ProjectFormWidget;
use App\Form\CallOfProject\CallOfProjectInformationType;
use App\Manager\CallOfProject\CallOfProjectManagerInterface;
use App\Manager\Project\ProjectManagerInterface;
use App\Manager\ProjectFormWidget\ProjectFormWidgetManagerInterface;
use App\Widget\FormWidget\FormWidgetInterface;
use App\Widget\WidgetManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/call-of-project", name="call_of_project.")
 */
class CallOfProjectController extends AbstractController
{

    /**
     * @Route("/", name="index", methods={"GET"})
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function index(EntityManagerInterface $em)
    {
        return $this->render('call_of_project/index.html.twig', [
            'call_of_projects' => $em->getRepository(CallOfProject::class)->findBy(
                ['createdBy' => $this->getUser()],
                ['createdAt' => 'ASC']
            ),
        ]);
    }

    /**
     * @Route("/all", name="all", methods={"GET"})
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function all(EntityManagerInterface $em)
    {
        return $this->render('call_of_project/all.html.twig', [
            'call_of_projects' => $em->getRepository(CallOfProject::class)->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="new", methods={"GET","POST"})
     * @param Request $request
     * @param CallOfProjectManagerInterface $callOfProjectManager
     * @return Response
     */
    public function new(Request $request, CallOfProjectManagerInterface $callOfProjectManager): Response
    {
        $callOfProject = $callOfProjectManager->create();
        $form = $this->createForm(CallOfProjectInformationType::class, $callOfProject);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $callOfProjectManager->save($callOfProject);

            return $this->redirectToRoute('app.call_of_project.form', [
                'id' => $callOfProject->getId()
            ]);
        }

        return $this->render('call_of_project/new.html.twig', [
            'call_of_project' => $callOfProject,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/projects", name="projects", methods={"GET"})
     * @param CallOfProject $callOfProject
     * @param Request $request
     * @return Response
     */
    public function projects(CallOfProject $callOfProject, Request $request): Response
    {
        return $this->render('call_of_project/project_list.html.twig', [
            'call_of_project' => $callOfProject,
        ]);
    }

    /**
     * @Route("/{id}/add-project", name="add_project", methods={"GET", "POST"})
     * @param CallOfProject $callOfProject
     * @param Request $request
     * @param ProjectManagerInterface $projectManager
     * @param WidgetManager $widgetManager
     * @return Response
     * @throws \Exception
     */
    public function addProject(
        CallOfProject $callOfProject,
        Request $request,
        ProjectManagerInterface $projectManager,
        WidgetManager $widgetManager
    ): Response
    {
        $project = $projectManager->create($callOfProject);
        $dynamicForm = $widgetManager->getDynamicForm($project);

        $dynamicForm->handleRequest($request);

        if ($dynamicForm->isSubmitted() and $dynamicForm->isValid()) {

            $widgetManager->hydrateProjectContentsByForm($project->getProjectContents(), $dynamicForm);

            $projectManager->save($project);

            return $this->redirectToRoute('app.homepage');
        }

        return $this->render('call_of_project/add_project.html.twig', [
            'call_of_project' => $callOfProject,
            'dynamic_form_html' => $widgetManager->renderDynamicFormHtml(
                $dynamicForm,
                'partial/widget/_dynamic_form.html.twig'
            ),
        ]);
    }

    /**
     * @Route("/{id}/informations", name="informations", methods={"GET", "POST"})
     * @param Request $request
     * @param CallOfProject $callOfProject
     * @return Response
     */
    public function informations(Request $request, CallOfProject $callOfProject): Response
    {
        $callOfProjectClone = clone $callOfProject;

        $form = $this->createForm(CallOfProjectInformationType::class, $callOfProject);
        $form->handleRequest($request);

        $openEditionFormModal = false;
        if ($form->isSubmitted()) {

            if ($form->isValid()) {

                $this->getDoctrine()->getManager()->flush();

                return $this->redirectToRoute('app.call_of_project.informations', [
                    'id' => $callOfProject->getId()
                ]);
            }

            $openEditionFormModal = true;

        }

        return $this->render('call_of_project/informations.html.twig', [
            'call_of_project' => $callOfProjectClone,
            'form' => $form->createView(),
            'open_edition_form_modal' => $openEditionFormModal
        ]);
    }

    /**
     * @Route("/{id}/form", name="form", methods={"GET","POST"})
     * @param CallOfProject $callOfProject
     * @param WidgetManager $widgetManager
     * @param Request $request
     * @param ProjectFormWidgetManagerInterface $projectFormWidgetManager
     * @param ProjectManagerInterface $projectManager
     * @return Response
     * @throws \Exception
     */
    public function form(
        CallOfProject $callOfProject,
        WidgetManager $widgetManager,
        Request $request,
        ProjectFormWidgetManagerInterface $projectFormWidgetManager,
        ProjectManagerInterface $projectManager
    ): Response
    {
        $widgetName = $request->query->get('widgetName');

        if ($widget = $widgetManager->getWidget($widgetName)) {
            $form = $this->createForm($widget->getFormType(), $widget);

            $form->handleRequest($request);

            if ($form->isSubmitted() and $form->isValid()) {

                $projectFormWidget = $projectFormWidgetManager->create();
                $projectFormWidget->setWidget($widget);
                $callOfProject->getProjectFormLayout()->addProjectFormWidget($projectFormWidget);

                $projectFormWidgetManager->save($projectFormWidget);

                return $this->redirectToRoute('app.call_of_project.form', [
                    'id' => $callOfProject->getId()
                ]);
            }
        }

        $project = $projectManager->create($callOfProject);
        $dynamicForm = $widgetManager->getDynamicForm($project, ['allWidgets' => true]);
        return $this->render('call_of_project/form.html.twig', [
            'call_of_project' => $callOfProject,
            'widget_manager' => $widgetManager,
            'dynamic_form_html' => $widgetManager->renderDynamicFormHtml(
                $dynamicForm,
                'partial/widget/_dynamic_form_demo.html.twig',
            ),
        ]);
    }


    /**
     * @Route("/{id}/get-widget-form", name="get_widget_form", methods={"GET"})
     * @param WidgetManager $widgetManager
     * @param Request $request
     * @return Response
     */
    public function getWidgetForm(
        WidgetManager $widgetManager,
        Request $request
    ): Response
    {
        $widgetName = $request->query->get('widgetName');

        if (!isset($widgetManager->getWidgets()[$widgetName])) {
            return $this->json(['success' => false]);
        }

        $widget = $widgetManager->getWidgets()[$widgetName];

        if (!$widget instanceof  FormWidgetInterface) {
            return $this->json(['success' => false]);
        }

        return $this->render($widget->getTemplate(), [
            'form' => $widget->getForm()->createView()
        ]);


    }

    /**
     * Route("/{id}", name="delete", methods={"DELETE"})
     */
    /*public function delete(Request $request, CallOfProject $callOfProject): Response
    {
        if ($this->isCsrfTokenValid('delete'.$callOfProject->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($callOfProject);
            $entityManager->flush();
        }

        return $this->redirectToRoute('index');
    }*/
}
