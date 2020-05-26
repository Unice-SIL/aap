<?php


namespace App\Controller\Admin;


use App\Entity\ProjectFormWidget;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class WidgetController
 * @package App\Controller\Admin
 * @Route("/widget", name="widget.")
 */
class WidgetController extends AbstractController
{
    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     * @param Request $request
     * @param ProjectFormWidget $projectFormWidget
     * @return Response
     */
    public function delete(Request $request, ProjectFormWidget $projectFormWidget): Response
    {
        if ($this->isCsrfTokenValid('delete'.$projectFormWidget->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($projectFormWidget);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app.admin.project_form_layout.configure', [
            'id' => $projectFormWidget->getProjectFormLayout()->getId()
        ]);
    }
}