<?php


namespace App\Controller\Admin;


use App\Entity\Group;
use App\Form\Group\GroupType;
use App\Manager\Group\GroupManagerInterface;
use App\Repository\GroupRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class GroupController
 * @package App\Controller\Admin
 * @Route("/group", name="group.")
 */
class GroupController extends AbstractController
{
    /**
     * @Route(name= "index", methods={"GET"})
     * @param GroupRepository $groupRepository
     * @return Response
     */
    public function index(GroupRepository $groupRepository){

        return $this->render('group/index.html.twig', [
            'groups' => $groupRepository->findAll()
        ]);
    }

    /**
     * @Route("/new", name="new", methods={"GET", "POST"})
     * @param GroupManagerInterface $groupManager
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return RedirectResponse|Response
     */
    public function new(GroupManagerInterface $groupManager, Request $request, TranslatorInterface $translator)
    {
        $group = $groupManager->create();
        $form = $this->createForm(GroupType::class, $group);
        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {

            $groupManager->save($group);

            $this->addFlash('success', $translator->trans('app.flash_message.create_success', [
                '%item%' => $group->getName()
            ]));

            return $this->redirectToRoute('app.admin.group.show', ['id' => $group->getId()]);

        }

        return $this->render('group/new.html.twig', [
            'form' => $form->createView(),
            'group' => $group
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit", methods={"GET", "POST"})
     * @param Group $group
     * @param GroupManagerInterface $groupManager
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return RedirectResponse|Response
     */
    public function edit(Group $group, GroupManagerInterface $groupManager, Request $request, TranslatorInterface $translator)
    {
        $form = $this->createForm(GroupType::class, $group);
        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {

            $groupManager->update($group);

            $this->addFlash('success', $translator->trans('app.flash_message.edit_success', [
                '%item%' => $group->getName()
            ]));

            return $this->redirectToRoute('app.admin.group.show', [
                'id' => $group->getId(),
            ]);

        }

        return $this->render('group/edit.html.twig', [
            'form' => $form->createView(),
            'group' => $group
        ]);
    }

    /**
     * @Route("/{id}/show", name="show", methods={"GET"})
     * @param Group $group
     */
    public function show(Group $group)
    {
        return $this->render('group/show.html.twig', [
            'group' => $group
        ]);
    }
}