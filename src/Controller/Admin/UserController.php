<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserType;
use App\Manager\User\UserManagerInterface;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/user", name="user.")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     * @param UserRepository $userRepository
     * @return Response
     */
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="new", methods={"GET","POST"})
     * @param Request $request
     * @param TranslatorInterface $translator
     * @param UserManagerInterface $userManager
     * @return Response
     */
    public function new(Request $request, TranslatorInterface $translator, UserManagerInterface $userManager): Response
    {
        $user = $userManager->create();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', $translator->trans('app.flash_message.create_success', [
                '%item%' => $user->getUsername()
            ]));

            return $this->redirectToRoute('app.admin.user.index');
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/show", name="show", methods={"GET"})
     * @param User $user
     * @return Response
     */
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit", methods={"GET","POST"})
     * @param Request $request
     * @param User $user
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function edit(Request $request, User $user, TranslatorInterface $translator): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', $translator->trans('app.flash_message.edit_success', [
                '%item%' => $user->getUsername()
            ]));

            return $this->redirectToRoute('app.admin.user.index');
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Route("/{id}", name="delete", methods={"DELETE"})
     * @param Request $request
     * @param User $user
     * @return Response
     */
    /*public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app.admin.user.index');
    }*/

    /**
     * @Route("/list-all-select-2", name="list_all_select_2", methods={"GET"})
     * @param Request $request
     * @param UserRepository $userRepository
     * @return JsonResponse
     */
    public function listAllSelect2(Request $request, UserRepository $userRepository)
    {

        $query = $request->query->get('q');

        $users = array_map(function ($user) {
            return [
                'id' => $user->getId(),
                'text' => $user->getUsername()
            ];
        }, $userRepository->findByQuery($query));

        return $this->json($users);
    }
}
