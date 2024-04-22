<?php

namespace App\Controller\Front;

use App\Entity\CallOfProject;
use App\Entity\CallOfProjectMailTemplate;
use App\Entity\MailTemplate;
use App\Entity\Project;
use App\Entity\User;
use App\Form\CallOfProject\CallOfProjectAclsType;
use App\Form\CallOfProject\CallOfProjectInformationType;
use App\Form\CallOfProject\DeleteType;
use App\Form\CallOfProject\MailTemplateType;
use App\Form\Project\ProjectToStudyType;
use App\Form\ProjectFormLayoutType;
use App\Manager\CallOfProject\CallOfProjectManagerInterface;
use App\Manager\Project\ProjectManagerInterface;
use App\Manager\User\UserManagerInterface;
use App\Repository\CallOfProjectRepository;
use App\Security\CallOfProjectVoter;
use App\Security\OrganizingCenterVoter;
use App\Security\UserVoter;
use App\Utils\Batch\AddReportBatchAction;
use App\Utils\Batch\BatchActionManagerInterface;
use App\Widget\WidgetManager;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Workflow\Registry;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/call-of-project", name="call_of_project.")
 */
class CallOfProjectController extends AbstractController
{
    /**
     * @var Registry
     */
    private $workflowRegistry;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var Request|null
     */
    private $request;

    /**
     * @param EntityManagerInterface $em
     * @param RequestStack $requestStack
     * @param Registry $workflowRegistry
     * @param TranslatorInterface $translator
     */
    public function __construct(
        EntityManagerInterface $em,
        RequestStack $requestStack,
        Registry $workflowRegistry,
        TranslatorInterface $translator
    )
    {
        $this->em = $em;
        $this->request = $requestStack->getCurrentRequest();
        $this->translator = $translator;
        $this->workflowRegistry = $workflowRegistry;
    }

    /**
     * @Route("/", name="index", methods={"GET"})
     * @Security("is_granted(constant('App\\Security\\UserVoter::VIEW_ONE_ORGANIZING_CENTER_OR_CALL_OF_PROJECT_AT_LEAST'))")
     * @return Response
     */
    public function index()
    {

        return $this->render('call_of_project/index.html.twig', [
            'call_of_projects' => $this->em->getRepository(CallOfProject::class)->getIfUserHasOnePermissionAtLeast(
                $this->getUser()
            ),
        ]);
    }

    /**
     * @Route("/all", name="all", methods={"GET"})
     * @return Response
     */
    public function all(): Response
    {
        return $this->render('call_of_project/all.html.twig', [
            'call_of_projects' => $this->em->getRepository(CallOfProject::class)->findAll(),
        ]);
    }

    /**
     * @Route("/{id}/presentation-before-adding-project", name="presentation_before_adding_project", methods={"GET"})
     * @param CallOfProject $callOfProject
     * @return Response
     */
    public function presentationBeforeAddingProject(CallOfProject $callOfProject): Response
    {
        return $this->render('call_of_project/presentation_before_adding_project.html.twig', [
            'call_of_project' => $callOfProject
        ]);
    }

    /**
     * @Route("/new", name="new", methods={"GET","POST"})
     * @Security("is_granted(constant('App\\Security\\UserVoter::ADMIN_ONE_ORGANIZING_CENTER_AT_LEAST'))")
     * @param CallOfProjectManagerInterface $callOfProjectManager
     * @return Response
     */
    public function new(CallOfProjectManagerInterface $callOfProjectManager): Response
    {
        $this->denyAccessUnlessGranted(UserVoter::ADMIN_ONE_ORGANIZING_CENTER_AT_LEAST, $this->getUser());

        $callOfProject = $callOfProjectManager->create();
        $form = $this->createForm(CallOfProjectInformationType::class, $callOfProject);
        $form->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->request->getSession()->set('app.call_of_project.new_help', true);
            $callOfProjectManager->save($callOfProject);

            return $this->redirectToRoute('app.call_of_project.informations', [
                'id' => $callOfProject->getId()
            ]);
        }

        //If ajax request (means for dynamic field) we remove errors
        if ($this->request->isXmlHttpRequest()) {
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
     * @param ProjectManagerInterface $projectManager
     * @param WidgetManager $widgetManager
     * @return Response
     * @throws Exception
     * @IsGranted(App\Security\CallOfProjectVoter::OPEN, subject="callOfProject")
     */
    public function addProject(CallOfProject $callOfProject, ProjectManagerInterface $projectManager, WidgetManager $widgetManager): Response
    {
        /**
         * CallOfProject $callOfProject
         */
        $this->denyAccessUnlessGranted(CallOfProjectVoter::OPEN, $callOfProject);

        $project = $projectManager->create($callOfProject);
        $dynamicForm = $widgetManager->getDynamicForm($project);

        $dynamicForm->handleRequest($this->request);

        if ($dynamicForm->isSubmitted() and $dynamicForm->isValid()) {
            $widgetManager->hydrateProjectContentsByForm($project->getProjectContents(), $dynamicForm);

            $projectManager->save($project);

            $this->addFlash('success', $this->translator->trans('app.flash_message.create_success', ['%item%' => $project->getName()]));


            return $this->redirectToRoute('app.project.show', ['id' => $project->getId()]);
        }

        if (!$this->isGranted(CallOfProjectVoter::SUBMIT_PROJECT, $callOfProject)) {
            $this->addFlash('error', $this->translator->trans('app.flash_message.create_unauthorized'));
            return $this->redirectToRoute('app.call_of_project.presentation_before_adding_project', ['id' => $callOfProject->getId()]);
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
     * @param CallOfProject $callOfProject
     * @return Response
     * @IsGranted(App\Security\CallOfProjectVoter::SHOW_INFORMATIONS, subject="callOfProject")
     */
    public function informations(CallOfProject $callOfProject): Response
    {
        $session = $this->request->getSession();
        $helpNewCallOfProjects = false;
        if ($session->has('app.call_of_project.new_help')) {
            $session->remove('app.call_of_project.new_help');
            $helpNewCallOfProjects = true;
        }
        $callOfProjectClone = clone $callOfProject;

        $form = $this->createForm(CallOfProjectInformationType::class, $callOfProject);
        $form->handleRequest($this->request);

        $openEditionFormModal = false;
        if ($form->isSubmitted() && $this->isGranted(CallOfProjectVoter::EDIT, $callOfProject)) {

            if ($form->isValid()) {

                $this->getDoctrine()->getManager()->flush();

                $this->addFlash('success', $this->translator->trans(
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
            'open_edition_form_modal' => $openEditionFormModal,
            'help_new_call_of_projects' => $helpNewCallOfProjects
        ]);
    }

    /**
     * @Route("/{id}/toggle-subscription", name="toggle_subscription")
     * @param CallOfProject $callOfProject
     * @param UserManagerInterface $userManager
     * @return Response
     */
    public function toggleSubscription(CallOfProject $callOfProject, UserManagerInterface $userManager)
    {
        /** @var User $user */
        $user = $this->getUser();

        if ($user->getSubscriptions()->contains($callOfProject)) {
            $user->removeSubscription($callOfProject);
        } else {
            $user->addSubscription($callOfProject);
        }

        $userManager->update($user);
        return $this->redirect($this->request->headers->get('referer'));
    }

    /**
     * @Route("/{id}/projects", name="projects", methods={"GET", "POST"})
     * @param CallOfProject $callOfProject
     * @param BatchActionManagerInterface $batchManager
     * @return Response
     * @IsGranted(App\Security\CallOfProjectVoter::SHOW_PROJECTS, subject="callOfProject")
     */
    public function projects(CallOfProject $callOfProject, BatchActionManagerInterface $batchManager): Response
    {
        $projectToStudyForm = $this->createForm(ProjectToStudyType::class);
        $projectToStudyForm->handleRequest($this->request);
        if ($projectToStudyForm->isSubmitted() and $projectToStudyForm->isValid()) {
            $this->toReview($callOfProject);
            $this->em->flush();
            return $this->redirectToRoute('app.call_of_project.projects', ['id' => $callOfProject->getId()]);
        }

        $batchActionBlackList = [];
        if (
            $callOfProject->getProjects()->filter(function ($project) {
                return $project->getStatus() !== Project::STATUS_STUDYING;
            })->count() > 0
        ) {
            $batchActionBlackList[] = AddReportBatchAction::class;
        }
        $batchActionForm = $batchManager->getForm(Project::class, $batchActionBlackList);
        $batchActionForm->handleRequest($this->request);
        if ($batchActionForm->isSubmitted() and $batchActionForm->isValid()) {
            $ok = $batchManager->saveDataInSession($batchActionForm, $this->request->getSession());
            if (!$ok) {
                $this->addFlash('error', $this->translator->trans('app.error_occured'));
                return $this->redirectToRoute('app.call_of_project.projects', ['id' => $callOfProject->getId()]);
            }
            return $this->redirectToRoute('app.call_of_project.batch_action', ['id' => $callOfProject->getId()]);
        }

        return $this->render('call_of_project/project_list.html.twig', [
            'call_of_project' => $callOfProject,
            'project_to_study_form' => $projectToStudyForm->createView(),
            'batch_action_form' => $batchActionForm->createView()
        ]);
    }


    /**
     * @Route("/{id}/reports", name="reports", methods={"GET", "POST"})
     * @param CallOfProject $callOfProject
     * @return Response
     * @IsGranted(App\Security\CallOfProjectVoter::SHOW_PROJECTS, subject="callOfProject")
     */
    public function reports(CallOfProject $callOfProject): Response
    {
        return $this->render('call_of_project/reports_list.html.twig', [
            'call_of_project' => $callOfProject,
        ]);
    }

    /**
     * @Route("/{id}/batch-action", name="batch_action", methods={"GET", "POST"})
     * @param CallOfProject $callOfProject
     * @param BatchActionManagerInterface $batchActionManager
     * @return Response
     */
    public function batchAction(CallOfProject $callOfProject, BatchActionManagerInterface $batchActionManager): Response
    {

        $entities = $batchActionManager->getEntitiesFromSession($this->request->getSession());
        $batchAction = $batchActionManager->getBatchActionFromSession($this->request->getSession());

        if (empty($entities) or !$batchAction) {
            throw new NotFoundHttpException($this->translator->trans('app.page_not_found'));
        }

        $form = $this->createForm($batchAction->getFormClassName());
        $form->handleRequest($this->request);

        if ($form->isSubmitted() and $form->isValid()) {

            $className = null;
            $ids = array_map(function ($entity) use (&$className) {

                if ($className === null) {
                    $className = get_class($entity);
                }
                return $entity->getId();
            }, $entities);

            $entities = $this->em->getRepository($className)->findBy(['id' => $ids]);

            foreach ($entities as $entity) {
                $batchAction->process($entity, $form);
            }

            $batchActionManager->removeBatchActionFromSession($this->request->getSession());

            $this->em->flush();
            $this->addFlash('success', $this->translator->trans('app.batch_action.success'));

            return $this->redirectToRoute('app.call_of_project.projects', ['id' => $callOfProject->getId()]);
        }

        return $this->render('call_of_project/batch_action.html.twig', [
            'call_of_project' => $callOfProject,
            'entities' => $entities,
            'batch_action' => $batchAction,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/show-permissions", name="show_permissions", methods={"GET"})
     * @param CallOfProject $callOfProject
     * @IsGranted(App\Security\CallOfProjectVoter::SHOW_PERMISSIONS, subject="callOfProject")
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
     * @return Response
     * @IsGranted(App\Security\CallOfProjectVoter::ADMIN, subject="callOfProject")
     */
    public function editPermissions(CallOfProject $callOfProject): Response
    {
        $form = $this->createForm(CallOfProjectAclsType::class, $callOfProject);

        $form->handleRequest($this->request);

        if ($form->isSubmitted() and $form->isValid()) {

            $this->em->flush();

            $this->addFlash(
                'success',
                $this->translator->trans('app.flash_message.edit_success', ['%item%' => $callOfProject->getName()])
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
     * @IsGranted(App\Security\CallOfProjectVoter::ADMIN, subject="callOfProject")
     * @throws Exception
     */
    public function form(CallOfProject $callOfProject, WidgetManager $widgetManager, ProjectManagerInterface $projectManager): Response
    {

        $project = $projectManager->create($callOfProject);
        $dynamicForm = $widgetManager->getDynamicForm($project, ['allWidgets' => true]);
        $form = $this->createForm(ProjectFormLayoutType::class, $callOfProject->getProjectFormLayout());

        return $this->render('call_of_project/form.html.twig', [
            'form' => $form->createView(),
            'call_of_project' => $callOfProject,
            'widget_manager' => $widgetManager,
            'dynamic_form_html' => $widgetManager->renderDynamicFormHtml(
                $dynamicForm,
                'partial/widget/_dynamic_form_demo.html.twig'
            ),
        ]);
    }

    /**
     * @Route("/{id}/mail-template", name="mail_template", methods={"GET", "POST"})
     * @IsGranted(App\Security\CallOfProjectVoter::ADMIN, subject="callOfProject")
     * @param CallOfProject $callOfProject
     * @return Response
     */
    public function listMailTemplates(CallOfProject $callOfProject): Response
    {
        $genericMailTemplates = $this->em->getRepository(MailTemplate::class)->findAll();
        $copMailTemplates = $this->em->getRepository(CallOfProjectMailTemplate::class)->findByCallOfProject($callOfProject->getId());

        $newMailTemplates = array_udiff($genericMailTemplates, $copMailTemplates, function (MailTemplate $mt1, MailTemplate $mt2) {
            return in_array($mt1->getName(), CallOfProjectMailTemplate::ALLOWED_TEMPLATES) && $mt1->getName() <=> $mt2->getName();
        });

        foreach ($newMailTemplates as $newMailTemplate) {
            $newCopMailTemplate = (new CallOfProjectMailTemplate())
                ->setCallOfProject($callOfProject)
                ->setName($newMailTemplate->getName())
                ->setSubject($newMailTemplate->getSubject())
                ->setBody($newMailTemplate->getBody())
                ->setEnable($newMailTemplate->isEnable());
            $this->em->persist($newCopMailTemplate);
        }
        $this->em->flush();

        return $this->render('call_of_project/list_mails_templates.html.twig', [
            'call_of_project' => $callOfProject
        ]);
    }

    /**
     * @Route("/{id}/mail-template/{mailTemplate}/edit", name="mail_template.edit", methods={"GET", "POST"})
     * @IsGranted(App\Security\CallOfProjectVoter::ADMIN, subject="callOfProject")
     */
    public function editMailTemplate(CallOfProject $callOfProject, CallOfProjectMailTemplate $mailTemplate)
    {

        $form = $this->createForm(MailTemplateType::class, $mailTemplate);
        $form->handleRequest($this->request);

        if ($form->isSubmitted() and $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success', $this->translator->trans('app.flash_message.edit_success', ['%item%' => $mailTemplate->getName()]));

            return $this->redirectToRoute('app.call_of_project.mail_template', [
                'id' => $callOfProject->getId()
            ]);
        }

        return  $this->render('call_of_project/edit_mail_template.html.twig', [
            'call_of_project' => $callOfProject,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/list-by-user-select-2", name="list_by_user_select_2", methods={"GET"})
     * @param CallOfProjectRepository $callOfProjectRepository
     * @return JsonResponse
     */
    public function listByUserSelect2(CallOfProjectRepository $callOfProjectRepository): JsonResponse
    {

        $this->denyAccessUnlessGranted(UserVoter::ADMIN_ONE_ORGANIZING_CENTER_AT_LEAST, $this->getUser());

        $query = $this->request->query->get('q');

        $callOfProjects = array_map(function ($callOfProject) {
            return [
                'id' => $callOfProject->getId(),
                'text' => $callOfProject->getName()
            ];
        }, $callOfProjectRepository->getByUserAndNameLikeQuery($this->getUser(), $query));
        return $this->json($callOfProjects);
    }

    /**
     * @Route("/list-all-select-2", name="list_all_select_2", methods={"GET"})
     * @param CallOfProjectRepository $callOfProjectRepository
     * @return JsonResponse
     */
    public function listAllSelect2(CallOfProjectRepository $callOfProjectRepository): JsonResponse
    {

        $this->denyAccessUnlessGranted(UserVoter::ADMIN_ONE_ORGANIZING_CENTER_OR_CALL_OF_PROJECT_AT_LEAST, $this->getUser());

        $query = $this->request->query->get('q');

        $callOfProjects = array_map(function ($callOfProject) {
            return [
                'id' => $callOfProject->getId(),
                'text' => $callOfProject->getName()
            ];
        }, $callOfProjectRepository->getLikeQuery($query));
        return $this->json($callOfProjects);
    }

    /**
     * @Route("/{id}/delete-form", name="delete_form", methods={"GET", "POST"})
     * @param CallOfProject $callOfProject
     * @param CallOfProjectManagerInterface $callOfProjectManager
     * @return RedirectResponse|Response
     */
    public function deleteForm(CallOfProject $callOfProject, CallOfProjectManagerInterface $callOfProjectManager)
    {
        $this->denyAccessUnlessGranted(OrganizingCenterVoter::ADMIN, $callOfProject->getOrganizingCenter());

        $form = $this->createForm(DeleteType::class, $callOfProject);
        $form->handleRequest($this->request);

        if ($form->isSubmitted() and $form->isValid()) {
            $name = $callOfProject->getName();
            $callOfProjectManager->delete($callOfProject);

            $this->addFlash('success', $this->translator->trans('app.flash_message.delete_success', [
                '%item%' => $name
            ]));

            return $this->redirectToRoute('app.call_of_project.index');
        }

        return $this->render('call_of_project/delete_form.html.twig', [
            'call_of_project' => $callOfProject,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/form/preview", name="form_preview", methods={"GET","POST"})
     * @param CallOfProject $callOfProject
     * @param WidgetManager $widgetManager
     * @param ProjectManagerInterface $projectManager
     * @return Response
     * @IsGranted(App\Security\CallOfProjectVoter::ADMIN, subject="callOfProject")
     * @throws Exception
     */
    public function formPreview(CallOfProject $callOfProject, WidgetManager $widgetManager, ProjectManagerInterface $projectManager): Response
    {
        $project = $projectManager->create($callOfProject);
        $dynamicForm = $widgetManager->getDynamicForm($project);

        return $this->render('call_of_project/form_preview.html.twig', [
            'call_of_project' => $callOfProject,
            'widget_manager' => $widgetManager,
            'dynamic_form_html' => $widgetManager->renderDynamicFormHtml(
                $dynamicForm,
                'partial/widget/_dynamic_form_preview.html.twig'
            ),
        ]);
    }

    /**
     * @IsGranted(App\Security\CallOfProjectVoter::FINISHED, subject="callOfProject")
     * @Route("/{id}/finished", name="finished", methods={"GET"})
     */
    public function finished(CallOfProject $callOfProject): RedirectResponse
    {
        $token = $this->request->query->get('token');
        if ($this->isCsrfTokenValid('close-call-of-projects', $token)) {

            $callOfProject->setStatus(CallOfProject::STATUS_FINISHED);
            $this->em->flush();
        }
        return $this->redirectToRoute('app.call_of_project.informations', [
            'id' => $callOfProject->getId()
        ]);
    }

    /**
     * @param CallOfProject $callOfProject
     * @IsGranted(App\Security\CallOfProjectVoter::TO_STUDY_MASS, subject="callOfProject")
     * @return void
     */
    private function toReview(CallOfProject $callOfProject)
    {
        foreach ($callOfProject->getProjects() as $project) {
            $stateMachine = $this->workflowRegistry->get($project, 'project_validation_process');
            try {
                if ($stateMachine->can($project, 'to_study')) {
                    $stateMachine->apply($project, 'to_study');
                }
            } catch (Exception $exception) {
                $this->addFlash('error', $this->translator->trans('app.flash_message.error_project_to_study', ['%item%' => $project->getName()]));
            }
        }
        $callOfProject->setStatus(CallOfProject::STATUS_REVIEW);
    }
}
