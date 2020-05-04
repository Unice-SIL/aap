<?php


namespace App\Controller;


use App\Entity\Project;
use App\Manager\Project\ProjectManagerInterface;
use App\Widget\WidgetManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ProjectController
 * @package App\Controller
 * @Route("/project", name="project.")
 */
class ProjectController extends AbstractController
{
    /**
     * @Route("/index", name="index", methods={"GET"})
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function index(EntityManagerInterface $em)
    {
        return $this->render('project/index.html.twig', [
            'projects' => $em->getRepository(Project::class)->findBy(
                ['createdBy' => $this->getUser()],
                ['createdAt' => 'DESC']
            ),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit", methods={"GET", "POST"})
     * @param Project $project
     * @param WidgetManager $widgetManager
     * @param Request $request
     * @param ProjectManagerInterface $projectManager
     * @return Response
     * @throws \Exception
     */
    public function edit(
        Project $project,
        WidgetManager $widgetManager,
        Request $request,
        ProjectManagerInterface $projectManager
    )
    {
        $projectManager->refreshProjectContents($project);

        $dynamicForm = $widgetManager->getDynamicForm($project);

        $dynamicForm->handleRequest($request);

        if ($dynamicForm->isSubmitted() and $dynamicForm->isValid()) {

            $widgetManager->hydrateProjectContentsByForm($project->getProjectContents(), $dynamicForm);

            $projectManager->update($project);

            return $this->redirectToRoute('app.project.edit', ['id' => $project->getId()]);
        }

        return $this->render('project/edit.html.twig', [
            'project' => $project,
            'dynamic_form_html' => $widgetManager->renderDynamicFormHtml(
                $dynamicForm,
                'partial/widget/_dynamic_form.html.twig'
            ),
        ]);
    }

    /**
     * @Route("/{id}/show", name="show", methods={"GET"})
     * @param Project $project
     * @return Response
     */
    public function show(Project $project)
    {
        return $this->render('project/show.html.twig', [
            'project' => $project
        ]);
    }
}