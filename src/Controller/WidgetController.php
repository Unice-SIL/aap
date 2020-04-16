<?php


namespace App\Controller;


use App\Entity\ProjectFormLayout;
use App\Widget\WidgetManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class WidgetController
 * @package App\Controller
 * @Route("/widget", name="widget.")
 */
class WidgetController extends AbstractController
{
    /**
     * @Route("/{id}/get-form", name="get_widget_form", methods={"GET"})
     * @param ProjectFormLayout $projectFormLayout
     * @param WidgetManager $widgetManager
     * @param Request $request
     * @param RouterInterface $router
     * @return Response
     */
    public function getWidgetForm(
        ProjectFormLayout $projectFormLayout,
        WidgetManager $widgetManager,
        Request $request,
        RouterInterface $router
    ): Response
    {
        $widgetName = $request->query->get('widgetName');

        if (!$widget = $widgetManager->getWidget($widgetName)) {
            return $this->json(['success' => false]);
        }

        $form = $this->createForm($widget->getFormType(), null, [
            'action' => $router->generate('app.call_of_project.form', [
                'id' => $projectFormLayout->getCallOfProject()->getId(),
                'widgetName' => $widgetName
            ])
        ]);

        return $this->render($widget->getTemplate(), [
            'form' => $form->createView()
        ]);
    }
}