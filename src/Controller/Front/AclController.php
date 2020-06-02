<?php


namespace App\Controller\Front;


use App\Repository\GroupRepository;
use App\Repository\UserRepository;
use App\Security\UserVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class AclController
 * @package App\Controller\Front
 * @Route("/acl", name="acl.")
 */
class AclController extends AbstractController
{
    /**
     * @Route("/list-user-and-group-select-2", name="list_user_and_group_select_2", methods={"GET"})
     * @param Request $request
     * @param UserRepository $userRepository
     * @param GroupRepository $groupRepository
     * @param TranslatorInterface $translator
     * @return JsonResponse
     */
    public function listAllSelect2(
        Request $request,
        UserRepository $userRepository,
        GroupRepository $groupRepository,
        TranslatorInterface $translator
    )
    {

        $this->denyAccessUnlessGranted(UserVoter::ADMIN_ONE_ORGANIZING_CENTER_OR_CALL_OF_PROJECT_AT_LEAST, $this->getUser());

        $query = $request->query->get('q');

        $users = array_map(function ($user) use ($translator){
            return [
                'id' => $user->getId(),
                'text' => sprintf('(%s) %s', $translator->trans('app.user.singular_label'), $user->getUsername())
            ];
        }, $userRepository->findByQuery($query));

        $groups = array_map(function ($group) use ($translator) {
            return [
                'id' => $group->getId(),
                'text' => sprintf('(%s) %s', $translator->trans('app.group.label'), $group->getName())
            ];
        }, $groupRepository->findByQuery($query));



        return $this->json(['results' => array_merge($users, $groups)]);
    }
}