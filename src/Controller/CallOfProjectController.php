<?php

namespace App\Controller;

use App\Entity\CallOfProject;
use App\Form\CallOfProject\CallOfProjectInformationType;
use App\Manager\CallOfProject\CallOfProjectManagerInterface;
use App\Widget\FormWidget\FormWidgetInterface;
use App\Widget\WidgetManager;
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

            return $this->redirectToRoute('app.call_of_project.projects', [
                'id' => $callOfProject->getId()
            ]);
        }

        return $this->render('call_of_project/new.html.twig', [
            'call_of_project' => $callOfProject,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/projects", name="projects", methods={"GET"})
     * @param CallOfProject $callOfProject
     * @param Request $request
     * @return Response
     */
    public function projects(CallOfProject $callOfProject, Request $request): Response
    {
        return $this->render('call_of_project/project_list.html.twig', [
            'call_of_project' => $callOfProject,
        ]);
    }

    /**
     * @Route("/{id}/informations", name="informations", methods={"GET"})
     * @param CallOfProject $callOfProject
     * @return Response
     */
    public function informations(CallOfProject $callOfProject): Response
    {
        return $this->render('call_of_project/informations.html.twig', [
            'call_of_project' => $callOfProject,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit", methods={"GET","POST"})
     * @param Request $request
     * @param CallOfProject $callOfProject
     * @return Response
     */
    public function edit(Request $request, CallOfProject $callOfProject): Response
    {
        $form = $this->createForm(CallOfProjectInformationType::class, $callOfProject);
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
     * @param WidgetManager $widgetManager
     * @return Response
     */
    public function form(CallOfProject $callOfProject, WidgetManager $widgetManager): Response
    {
        return $this->render('call_of_project/form.html.twig', [
            'call_of_project' => $callOfProject,
            'widget_manager' => $widgetManager
        ]);
    }

    /**
     * @Route("/{id}/get-widget-form", name="get_widget_form", methods={"GET"})
     * @param CallOfProject $callOfProject
     * @param WidgetManager $widgetManager
     * @param Request $request
     * @return Response
     */
    public function getWidgetForm(
        CallOfProject $callOfProject,
        WidgetManager $widgetManager,
        Request $request
    ): Response
    {
        $widgetName = $request->query->get('widgetName');

        if (!isset($widgetManager->getWidgets()[$widgetName])) {
            return $this->json(['success' => false]);
        }

        $widget = $widgetManager->getWidgets()[$widgetName];

        if (!$widget instanceof  FormWidgetInterface) {
            return $this->json(['success' => false]);
        }

        return $this->render($widget->getTemplate(), [
            'form' => $widget->getForm()->createView()
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
