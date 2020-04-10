<?php

namespace App\Controller\Admin;

use App\Entity\CallOfProject;
use App\Form\Admin\CallOfProject\CallOfProjectType;
use App\Repository\CallOfProjectRepository;
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
     */
    public function index(CallOfProjectRepository $callOfProjectRepository): Response
    {
        return $this->render('call_of_project/index.html.twig', [
            'call_of_projects' => $callOfProjectRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $callOfProject = new CallOfProject();
        $form = $this->createForm(CallOfProjectType::class, $callOfProject);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($callOfProject);
            $entityManager->flush();

            return $this->redirectToRoute('app.admin.call_of_project.index');
        }

        return $this->render('call_of_project/new.html.twig', [
            'call_of_project' => $callOfProject,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Route("/{id}", name="show", methods={"GET"})
     */
    /*public function show(CallOfProject $callOfProject): Response
    {
        return $this->render('call_of_project/show.html.twig', [
            'call_of_project' => $callOfProject,
        ]);
    }*/

    /**
     * @Route("/{id}/edit", name="edit", methods={"GET","POST"})
     */
    public function edit(Request $request, CallOfProject $callOfProject): Response
    {
        $form = $this->createForm(CallOfProjectType::class, $callOfProject);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('app.admin.call_of_project.index');
        }

        return $this->render('call_of_project/edit.html.twig', [
            'call_of_project' => $callOfProject,
            'form' => $form->createView(),
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
