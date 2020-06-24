<?php


namespace App\Controller\Front;


use App\Repository\ProjectFormWidgetRepository;
use App\Security\UserVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ProjectFormWidgetController
 * @package App\Controller\Front
 * @Route("/project-form-widget", name="project_form_widget.")
 */
class ProjectFormWidgetController extends AbstractController
{
    /**
     * @Route("/list-all-select-2", name="list_all_select_2", methods={"GET"})
     * @param Request $request
     * @param ProjectFormWidgetRepository $projectFormWidgetRepository
     * @return JsonResponse
     */
    public function listAllTemplatesSelect2(Request $request, ProjectFormWidgetRepository $projectFormWidgetRepository)
    {

        $this->denyAccessUnlessGranted(UserVoter::ADMIN_ONE_ORGANIZING_CENTER_OR_CALL_OF_PROJECT_AT_LEAST, $this->getUser());

        $query = $request->query->get('q');
        $callOfProjectId = $request->query->get('call_of_project_id');

        $projectFormWidgets = array_map(function ($projectFormWidget) {
            return [
                'id' => $projectFormWidget->getId(),
                'text' => $projectFormWidget->getTitle()
            ];
        }, $projectFormWidgetRepository->getLikeQuery($query, $callOfProjectId));

        return $this->json($projectFormWidgets);
    }
}