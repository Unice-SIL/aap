<?php

namespace App\Controller;

use App\Entity\CallOfProject;
use App\Form\CallOfProject\CallOfProjectInformationType;
use App\Form\CallOfProject\CallOfProjectAclsType;
use App\Form\Project\ProjectToStudyType;
use App\Manager\CallOfProject\CallOfProjectManagerInterface;
use App\Manager\Project\ProjectManagerInterface;
use App\Repository\CallOfProjectRepository;
use App\Security\CallOfProjectVoter;
use App\Utils\Breadcrumb\BreadcrumbManager;
use App\Widget\WidgetManager;
use Doctrine\ORM\EntityManagerInterface;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Workflow\Registry;
use Symfony\Contracts\Translation\TranslatorInterface;

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

        //If ajax request (means for dynamic field) we remove errors
        if ($request->isXmlHttpRequest()) {
            $form->clearErrors(true);
        }

        return $this->render('call_of_project/new.html.twig', [
            'call_of_project' => $callOfProject,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/add-project", name="add_project", methods={"GET", "POST"})
     * @param CallOfProject $callOfProject
     * @param Request $request
     * @param ProjectManagerInterface $projectManager
     * @param WidgetManager $widgetManager
     * @param TranslatorInterface $translator
     * @return Response
     * @throws \Exception
     */
    public function addProject(
        CallOfProject $callOfProject,
        Request $request,
        ProjectManagerInterface $projectManager,
        WidgetManager $widgetManager,
        TranslatorInterface $translator
    ): Response
    {
        $this->denyAccessUnlessGranted(CallOfProjectVoter::OPEN, $callOfProject);

        $project = $projectManager->create($callOfProject);
        $dynamicForm = $widgetManager->getDynamicForm($project);

        $dynamicForm->handleRequest($request);

        if ($dynamicForm->isSubmitted() and $dynamicForm->isValid()) {

            $widgetManager->hydrateProjectContentsByForm($project->getProjectContents(), $dynamicForm);

            $projectManager->save($project);

            $this->addFlash('success', $translator->trans('app.flash_message.create_success', ['%item%' => $project->getName()]));

            return $this->redirectToRoute('app.project.show', ['id' => $project->getId()]);
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
     * @param BreadcrumbManager $breadcrumbManager
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function informations(
        Request $request,
        CallOfProject $callOfProject,
        BreadcrumbManager $breadcrumbManager,
        TranslatorInterface $translator
    ): Response
    {

        $callOfProjectClone = clone $callOfProject;

        $form = $this->createForm(CallOfProjectInformationType::class, $callOfProject);
        $form->handleRequest($request);

        $openEditionFormModal = false;
        if ($form->isSubmitted()) {

            if ($form->isValid()) {

                $this->getDoctrine()->getManager()->flush();

                $this->addFlash('success', $translator->trans(
                    'app.flash_message.edit_success', [
                        '%item%' => $callOfProject->getName()
                ]));
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
     * @Route("/{id}/projects", name="projects", methods={"GET", "POST"})
     * @param CallOfProject $callOfProject
     * @param Request $request
     * @param Registry $workflowRegistry
     * @return Response
     */
    public function projects(CallOfProject $callOfProject, Request $request, Registry $workflowRegistry, EntityManagerInterface $em): Response
    {
        $projectToStudyForm = $this->createForm(ProjectToStudyType::class);
        $projectToStudyForm->handleRequest($request);

        if ($projectToStudyForm->isSubmitted() and $projectToStudyForm->isValid()) {

            $this->denyAccessUnlessGranted(CallOfProjectVoter::TO_STUDY_MASS, $callOfProject);

            foreach ($callOfProject->getProjects() as $project) {

                $stateMachine = $workflowRegistry->get($project, 'project_validation_process');

                try {
                    $stateMachine->apply($project, 'to_study');
                } catch (LogicException $exception) {
                    // ...
                }

            }

            $em->flush();

            return  $this->redirectToRoute('app.call_of_project.projects', ['id' => $callOfProject->getId()]);
        }

        return $this->render('call_of_project/project_list.html.twig', [
            'call_of_project' => $callOfProject,
            'project_to_study_form' => $projectToStudyForm->createView(),
        ]);
    }

    /**
     * @Route("/{id}/show-permissions", name="show_permissions", methods={"GET"})
     * @param CallOfProject $callOfProject
     * @return Response
     */
    public function showPermissions(CallOfProject $callOfProject): Response
    {
        return $this->render('call_of_project/show_permissions.html.twig', [
            'call_of_project' => $callOfProject,
        ]);
    }

    /**
     * @Route("/{id}/edit-permissions", name="edit_permissions", methods={"GET", "POST"})
     * @param CallOfProject $callOfProject
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function editPermissions(
        CallOfProject $callOfProject,
        Request $request,
        EntityManagerInterface $em,
        TranslatorInterface $translator
    ): Response
    {
        $form = $this->createForm(CallOfProjectAclsType::class, $callOfProject);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {

            $em->flush();

            $this->addFlash(
                'success',
                $translator->trans('app.flash_message.edit_success', ['%item%' => $callOfProject->getName()])
            );

            return $this->redirectToRoute('app.call_of_project.show_permissions', ['id' => $callOfProject->getId()]);
        }
        return $this->render('call_of_project/edit_permissions.html.twig', [
            'call_of_project' => $callOfProject,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/form", name="form", methods={"GET","POST"})
     * @param CallOfProject $callOfProject
     * @param WidgetManager $widgetManager
     * @param ProjectManagerInterface $projectManager
     * @return Response
     * @throws \Exception
     */
    public function form(
        CallOfProject $callOfProject,
        WidgetManager $widgetManager,
        ProjectManagerInterface $projectManager
    ): Response
    {

        $project = $projectManager->create($callOfProject);
        $dynamicForm = $widgetManager->getDynamicForm($project, ['allWidgets' => true]);

        return $this->render('call_of_project/form.html.twig', [
            'call_of_project' => $callOfProject,
            'widget_manager' => $widgetManager,
            'dynamic_form_html' => $widgetManager->renderDynamicFormHtml(
                $dynamicForm,
                'partial/widget/_dynamic_form_demo.html.twig'
            ),
        ]);
    }

    /**
     * @Route("/list-by-user-select-2", name="list_by_user_select_2", methods={"GET"})
     * @param Request $request
     * @param CallOfProjectRepository $callOfProjectRepository
     * @return JsonResponse
     */
    public function listByUserSelect2(Request $request, CallOfProjectRepository $callOfProjectRepository)
    {

        $query = $request->query->get('q');

        $callOfProjects = array_map(function ($callOfProject) {
            return [
                'id' => $callOfProject->getId(),
                'text' => $callOfProject->getName()
            ];
        }, $callOfProjectRepository->getByUserAndNameLikeQuery($this->getUser(), $query));
        return $this->json($callOfProjects);
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
