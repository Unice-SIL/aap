<?php

namespace App\Controller\Front;

use App\Repository\OrganizingCenterRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/organizing-center", name="organizing_center.")
 */
class OrganizingCenterController extends AbstractController
{
    /**
     * @Route("/list-by-user-and-permissions-select-2", name="list_by_user_and_permissions_select_2", methods={"GET"})
     * @param Request $request
     * @param OrganizingCenterRepository $organizingCenterRepository
     * @return JsonResponse
     */
    public function listByUserSelect2(Request $request, OrganizingCenterRepository $organizingCenterRepository): JsonResponse
    {

        $query = $request->query->get('q');

        $organizingCenters = array_map(function ($organizingCenter) {
            return [
                'id' => $organizingCenter->getId(),
                'text' => $organizingCenter->getName()
            ];
        }, $organizingCenterRepository->getByUserPermissionsLikeQuery($this->getUser(), $query));
        return $this->json($organizingCenters);
    }
}