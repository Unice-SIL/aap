<?php


namespace App\Controller\Front;


use App\Entity\Invitation;
use App\Repository\UserRepository;
use App\Security\UserVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class UserController
 * @package App\Controller\Front
 * @Route("/user", name="user.")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/list-all-select-2", name="list_all_select_2", methods={"GET"})
     * @param Request $request
     * @param UserRepository $userRepository
     * @param TranslatorInterface $translator
     * @return JsonResponse
     */
    public function listAllSelect2(Request $request, UserRepository $userRepository, TranslatorInterface $translator)
    {

        $this->denyAccessUnlessGranted(UserVoter::ADMIN_ONE_ORGANIZING_CENTER_OR_CALL_OF_PROJECT_AT_LEAST, $this->getUser());

        $query = $request->query->get('q');

        $users = array_map(function ($user) use ($translator){
            return [
                'id' => $user->getId(),
                'text' => $user->getUsername()
            ];
        }, $userRepository->findByQuery($query));

        return $this->json($users);
    }
}