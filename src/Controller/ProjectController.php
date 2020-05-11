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
use Symfony\Contracts\Translation\TranslatorInterface;

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
     * @param TranslatorInterface $translator
     * @return Response
     * @throws \Exception
     */
    public function edit(
        Project $project,
        WidgetManager $widgetManager,
        Request $request,
        ProjectManagerInterface $projectManager,
        TranslatorInterface $translator
    )
    {
        $context = $request->query->get('context');

        if ($context === 'call_of_project') {
            $layout = 'call_of_project/layout.html.twig';
        }

        $projectManager->refreshProjectContents($project);

        $dynamicForm = $widgetManager->getDynamicForm($project);

        $dynamicForm->handleRequest($request);

        if ($dynamicForm->isSubmitted() and $dynamicForm->isValid()) {

            $widgetManager->hydrateProjectContentsByForm($project->getProjectContents(), $dynamicForm);

            $projectManager->update($project);

            $this->addFlash('success', $translator->trans('app.flash_message.edit_success', ['%item%' => $project->getName()]));

            return $this->redirectToRoute('app.project.show', ['id' => $project->getId()]);
        }

        return $this->render('project/edit.html.twig', [
            'project' => $project,
            'call_of_project' => $project->getCallOfProject(),
            'layout' => $layout ?? null,
            'context' => $context,
            'dynamic_form_html' => $widgetManager->renderDynamicFormHtml(
                $dynamicForm,
                'partial/widget/_dynamic_form.html.twig'
            ),
        ]);
    }

    /**
     * @Route("/{id}/show", name="show", methods={"GET"})
     * @param Project $project
     * @param Request $request
     * @return Response
     */
    public function show(Project $project, Request $request)
    {
        $context = $request->query->get('context');

        if ($context === 'call_of_project') {
            $layout = 'call_of_project/layout.html.twig';
        }

        return $this->render('project/show.html.twig', [
            'project' => $project,
            'call_of_project' => $project->getCallOfProject(),
            'layout' => $layout ?? null,
            'context' => $context
        ]);
    }

}