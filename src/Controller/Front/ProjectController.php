<?php


namespace App\Controller\Front;


use App\Entity\Project;
use App\Entity\User;
use App\Form\Project\AddReporterType;
use App\Form\Project\ValidationType;
use App\Manager\Project\ProjectManagerInterface;
use App\Security\CallOfProjectVoter;
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
     * @param TranslatorInterface $translator
     * @param Registry $workflowRegistry
     * @param \Swift_Mailer $mailer
     * @return Response
     * @IsGranted(App\Security\ProjectVoter::SHOW, subject="project")
     */
    public function show(
        Project $project,
        Request $request,
        EntityManagerInterface $em,
        TranslatorInterface $translator,
        Registry $workflowRegistry,
        \Swift_Mailer $mailer
    )
    {
        $context = $request->query->get('context');
        $reporterAdded = $request->getSession()->remove('reporterAdded');


        $addReportersForm = $this->createForm(AddReporterType::class, $project);
        $addReportersForm->handleRequest($request);

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

        /** @var User $user */
        $user = $this->getUser();
        $processValidationForm = function (FormInterface $form) use ($workflowRegistry, $translator, $project, $em, $mailer, $user, $context) {

            $this->denyAccessUnlessGranted(CallOfProjectVoter::EDIT, $project->getCallOfProject());
            if ($project->getStatus() !== Project::STATUS_STUDYING) {
                throw new AccessDeniedException();
            }

            try {

                $transition = $form->get('action')->getData() === Project::STATUS_VALIDATED ? 'validate' : 'refused';
                $stateMachine = $workflowRegistry->get($project, 'project_validation_process');
                $stateMachine->apply($project, $transition);

            } catch (LogicException $exception) {
                $this->addFlash('error',
                    $translator->trans(
                        'app.flash_message.error_project_' . $transition, ['%item%' => $project->getName()]
                    )
                );
            }

            $this->addFlash('success',
                $translator->trans(
                    'app.flash_message.success_project_' . $transition, ['%item%' => $project->getName()]
                )
            );

            $em->flush();

            if ($form->get('automaticSending')->getData()) {
                $message = new \Swift_Message('Message de validation/refus', $form->get('mailTemplate')->getData());
                $message
                    ->setFrom($user->getEmail())
                    ->setTo($project->getCreatedBy()->getEmail())
                ;

                $mailer->send($message);
            }

            $routeParameters = ['id' => $project->getId()];
            if ($context === 'call_of_project') {
                $routeParameters['context'] = $context;
            }
            return $this->redirectToRoute('app.project.show', $routeParameters);

        };

        $validationForm = $this->createForm(ValidationType::class, $project, ['context' => Project::STATUS_VALIDATED]);
        $validationForm->handleRequest($request);
        if ($validationForm->isSubmitted() and $validationForm->isValid()) {
            if ($validationForm->has('action') and $validationForm->get('action')->getData() === Project::STATUS_VALIDATED) {
                return $processValidationForm($validationForm);
            }
        }

        $refusalForm = $this->createForm(ValidationType::class, $project, ['context' => Project::STATUS_REFUSED]);
        $refusalForm->handleRequest($request);

        if ($refusalForm->isSubmitted() and $refusalForm->isValid()) {
            if ($refusalForm->has('action') and $refusalForm->get('action')->getData() === Project::STATUS_REFUSED) {
                return $processValidationForm($refusalForm);
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
            'refusal_form' => $refusalForm->createView()
        ]);
    }

}