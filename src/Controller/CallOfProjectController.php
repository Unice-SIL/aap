<?php

namespace App\Controller;

use App\Entity\CallOfProject;
use App\Form\CallOfProject\CallOfProjectType;
use App\Manager\CallOfProject\CallOfProjectManagerInterface;
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
     * @param CallOfProjectRepository $callOfProjectRepository
     * @return Response
     */
    public function index(CallOfProjectRepository $callOfProjectRepository): Response
    {
        return $this->render('call_of_project/index.html.twig', [
            'call_of_projects' => $callOfProjectRepository->findAll(),
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
        $form = $this->createForm(CallOfProjectType::class, $callOfProject);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $callOfProjectManager->save($callOfProject);

            return $this->redirectToRoute('app.call_of_project.form', ['id' => $callOfProject->getId()]);
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

            return $this->redirectToRoute('app.call_of_project.index');
        }

        return $this->render('call_of_project/edit.html.twig', [
            'call_of_project' => $callOfProject,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/form", name="form", methods={"GET","POST"})
     * @param CallOfProject $callOfProject
     * @return Response
     */
    public function form(CallOfProject $callOfProject): Response
    {

        return $this->render('call_of_project/form.html.twig', [
            'call_of_project' => $callOfProject,
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
