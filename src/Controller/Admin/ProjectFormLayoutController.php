<?php


namespace App\Controller\Admin;

use App\Entity\ProjectFormLayout;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

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
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(EntityManagerInterface $entityManager)
    {
        return $this->render('project_form_layout.html.twig', [
            'templates' => $entityManager->getRepository(ProjectFormLayout::class)->findByIsTemplate(true),
        ]);
    }
}