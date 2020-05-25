<?php


namespace App\Controller\Admin;

use App\Entity\ProjectFormLayout;
use App\Form\ProjectFormLayout\ProjectFormLayoutInformationType;
use App\Manager\ProjectFormLayout\ProjectFormLayoutManagerInterface;
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

            return $this->redirectToRoute('app.admin.project_form_layout.index');
        }

        return $this->render('project_form_layout/new.html.twig', [
            'form' => $form->createView()
        ]);
    }
}