<?php


namespace App\Controller\Front;


use App\Entity\Project;
use App\Form\Project\AddReporterType;
use App\Manager\Project\ProjectManagerInterface;
use App\Widget\WidgetManager;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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
     * @IsGranted(App\Security\ProjectVoter::EDIT, subject="project")
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

            return $this->redirectToRoute('app.project.show', ['id' => $project->getId(), 'context' => $context]);
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
     * @Route("/{id}/show", name="show", methods={"GET", "POST"})
     * @param Project $project
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     * @IsGranted(App\Security\ProjectVoter::SHOW, subject="project")
     */
    public function show(Project $project, Request $request, EntityManagerInterface $em, TranslatorInterface $translator)
    {
        $context = $request->query->get('context');
        $reporterAdded = $request->getSession()->remove('reporterAdded');


        $addReportersForm = $this->createForm(AddReporterType::class, $project);
        $addReportersForm->handleRequest($request);

        if ($addReportersForm->isSubmitted() and $addReportersForm->isValid()) {

            $em->flush();

            $this->addFlash('success', $translator->trans('app.flash_message.edit_success', ['%item%' => $project->getName()]));

            $routeParameters = ['id' => $project->getId()];
            if ($context === 'call_of_project') {
                $routeParameters['context'] = $context;
            }

            $request->getSession()->set('reporterAdded', true);

            return $this->redirectToRoute('app.project.show', $routeParameters);
        }

        if ($context === 'call_of_project') {
            $layout = 'call_of_project/layout.html.twig';
        }


        return $this->render('project/show.html.twig', [
            'project' => $project,
            'call_of_project' => $project->getCallOfProject(),
            'layout' => $layout ?? null,
            'context' => $context,
            'add_reporters_form' => $addReportersForm->createView(),
            'reporter_added' => $reporterAdded
        ]);
    }

}