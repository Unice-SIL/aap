<?php


namespace App\Controller\Front;


use App\Entity\Invitation;
use App\Entity\User;
use App\Form\User\EditMyProfileType;
use App\Repository\UserRepository;
use App\Security\UserVoter;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
                'text' => sprintf('%s %s (%s)',
                        $user->getFirstname(),
                        $user->getLastname(),
                        $user->getEmail()
                    )
            ];
        }, $userRepository->findByQuery($query));

        return $this->json($users);
    }

    /**
     * @param User $user
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @return RedirectResponse
     * @Route("/{id}/delete-notifications", name="delete_notifications")
     */
    public function deleteNotifications(User $user, EntityManagerInterface $entityManager, Request $request)
    {
        if ($this->isCsrfTokenValid('delete'. $user->getId(), $request->request->get('_token'))) {
            foreach ($user->getNotifications() as $notification) {
                $entityManager->remove($notification);
            }

            $entityManager->flush();
        }

        return $this->redirect(
            $request->headers->get('referer')
        );

    }

    /**
     * @Route("/show-my-profile", name="show_my_profile", methods={"GET"})
     */
    public function showMyProfile()
    {
        return $this->render('user/show_my_profile.html.twig');
    }

    /**
     * @Route("/edit-my-profile", name="edit_my_profile", methods={"GET", "POST"})
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function editMyProfile(Request $request, EntityManagerInterface $entityManager, TranslatorInterface $translator)
    {
        $this->denyAccessUnlessGranted(UserVoter::AUTH_BASIC, $this->getUser());

        $form = $this->createForm(EditMyProfileType::class, $this->getUser());
        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', $translator->trans(
                'app.flash_message.edit_success', [
                '%item%' => $this->getUser()->getUsername()
            ]));

            return $this->redirectToRoute('app.user.show_my_profile');
        }
        return $this->render('user/edit_my_profile.html.twig', [
            'form' => $form->createView()
        ]);
    }
}