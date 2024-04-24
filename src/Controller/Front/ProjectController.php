<?php


namespace App\Controller\Front;


use App\Entity\Comment;
use App\Entity\Project;
use App\Entity\User;
use App\Exception\ProjectTransitionException;
use App\Form\Project\AddCommentType;
use App\Form\Project\AddReporterType;
use App\Form\Project\ValidationType;
use App\Manager\Notification\NotificationManagerInterface;
use App\Manager\Project\ProjectManagerInterface;
use App\Repository\CommentRepository;
use App\Security\CallOfProjectVoter;
use App\Utils\Mail\MailHelper;
use App\Widget\WidgetManager;
use Doctrine\ORM\EntityManagerInterface;
use LogicException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Workflow\Registry;
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
        Project                 $project,
        WidgetManager           $widgetManager,
        Request                 $request,
        ProjectManagerInterface $projectManager,
        TranslatorInterface     $translator
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
     * @IsGranted(App\Security\ProjectVoter::SHOW, subject="project")
     * @param Project $project
     * @param Request $request
     * @param ProjectManagerInterface $projectManager
     * @param EntityManagerInterface $em
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function show(
        Project $project,
        Request $request,
        ProjectManagerInterface $projectManager,
        EntityManagerInterface $em,
        TranslatorInterface $translator
    )
    {
        $context = $request->query->get('context');
        $reporterAdded = $request->getSession()->remove('reporterAdded');

        $addReportersForm = $this->createForm(AddReporterType::class, $project);
        $addCommentForm = $this->createForm(AddCommentType::class, new Comment());
        $addCommentForm->handleRequest($request);
        $addReportersForm->handleRequest($request);

        if ($addCommentForm->isSubmitted() && $addCommentForm->isValid()) {
            $this->denyAccessUnlessGranted(CallOfProjectVoter::EDIT, $project->getCallOfProject());
            /** @var Comment $comment */
            $comment = $addCommentForm->getData();
            $comment->setUser($this->getUser());
            $em->persist($comment);
            $project->addComment($comment);
            $em->flush();
            $this->addFlash('success', $translator->trans('app.flash_message.add_comment_success', ['%item%' => $project->getName()]));

            $routeParameters = ['id' => $project->getId()];
            if ($context === 'call_of_project') {
                $routeParameters['context'] = $context;
            }

            $request->getSession()->set('reporterAdded', true);

            return $this->redirectToRoute('app.project.show', $routeParameters);
        }

        if ($addReportersForm->isSubmitted() and $addReportersForm->isValid()) {

            $this->denyAccessUnlessGranted(CallOfProjectVoter::EDIT, $project->getCallOfProject());
            if ($project->getStatus() !== Project::STATUS_STUDYING) {
                throw new AccessDeniedException();
            }

            $em->flush();

            $this->addFlash('success', $translator->trans('app.flash_message.edit_success', ['%item%' => $project->getName()]));

            $routeParameters = ['id' => $project->getId()];
            if ($context === 'call_of_project') {
                $routeParameters['context'] = $context;
            }

            $request->getSession()->set('reporterAdded', true);

            return $this->redirectToRoute('app.project.show', $routeParameters);
        }

        $validationForm = $this->createForm(ValidationType::class, $project, ['context' => Project::TRANSITION_VALIDATE]);
        $validationForm->handleRequest($request);

        $refusalForm = $this->createForm(ValidationType::class, $project, ['context' => Project::TRANSITION_REFUSE]);
        $refusalForm->handleRequest($request);

        // If validation or refusal project form is submit and valid
        if ($validationForm->isSubmitted() and $validationForm->isValid() or $refusalForm->isSubmitted() and $refusalForm->isValid()) {

            // Check permission
            $this->denyAccessUnlessGranted(CallOfProjectVoter::EDIT, $project->getCallOfProject());
            if ($project->getStatus() !== Project::STATUS_STUDYING) {
                throw new AccessDeniedException();
            }

            // Get submitted form
            $form = $validationForm->isSubmitted() ? $validationForm : $refusalForm;
            if ($form->has('action')) {
                $transition = $form->get('action')->getData();
                if ($form->get('automaticSending')->getData()) {
                    $project->setValidateRejectMailContent($form->get('mailTemplate')->getData());
                }

                try {
                    $projectManager->validateOrRefuse($project, $transition);
                    $this->addFlash('success',
                        $translator->trans(
                            'app.flash_message.success_project_' . $transition, ['%item%' => $project->getName()]
                        )
                    );
                } catch (ProjectTransitionException $e) {
                    $this->addFlash('error', $e->getMessage());
                }

                $routeParameters = ['id' => $project->getId()];
                if ($context === 'call_of_project') {
                    $routeParameters['context'] = $context;
                }
                return $this->redirectToRoute('app.project.show', $routeParameters);
            }
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
            'reporter_added' => $reporterAdded,
            'validation_form' => $validationForm->createView(),
            'refusal_form' => $refusalForm->createView(),
            'add_comment_form' => $addCommentForm->createView()
        ]);
    }
}
