<?php

namespace App\Controller\Admin;

use App\Entity\OrganizingCenter;
use App\Form\OrganizingCenterType;
use App\Repository\OrganizingCenterRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/organizing-center", name="organizing_center.")
 */
class OrganizingCenterController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     * @param OrganizingCenterRepository $organizingCenterRepository
     * @return Response
     */
    public function index(OrganizingCenterRepository $organizingCenterRepository): Response
    {
        return $this->render('organizing_center/index.html.twig', [
            'organizing_centers' => $organizingCenterRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request, TranslatorInterface $translator): Response
    {
        $organizingCenter = new OrganizingCenter();
        $form = $this->createForm(OrganizingCenterType::class, $organizingCenter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($organizingCenter);
            $entityManager->flush();

            $this->addFlash('success', $translator->trans('app.flash_message.create_success', [
                '%item%' => $organizingCenter->getName()
            ]));

            return $this->redirectToRoute('app.admin.organizing_center.index');
        }

        return $this->render('organizing_center/new.html.twig', [
            'organizing_center' => $organizingCenter,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="show", methods={"GET"})
     * @param OrganizingCenter $organizingCenter
     * @return Response
     */
    public function show(OrganizingCenter $organizingCenter): Response
    {
        return $this->render('organizing_center/show.html.twig', [
            'organizing_center' => $organizingCenter,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit", methods={"GET","POST"})
     * @param Request $request
     * @param OrganizingCenter $organizingCenter
     * @return Response
     */
    public function edit(Request $request, OrganizingCenter $organizingCenter, TranslatorInterface $translator): Response
    {
        $form = $this->createForm(OrganizingCenterType::class, $organizingCenter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', $translator->trans('app.flash_message.edit_success', [
                '%item%' => $organizingCenter->getName()
            ]));

            return $this->redirectToRoute('app.admin.organizing_center.index');
        }

        return $this->render('organizing_center/edit.html.twig', [
            'organizing_center' => $organizingCenter,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     * @param Request $request
     * @param OrganizingCenter $organizingCenter
     * @return Response
     */
    public function delete(Request $request, OrganizingCenter $organizingCenter): Response
    {
        if ($this->isCsrfTokenValid('delete'.$organizingCenter->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($organizingCenter);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app.admin.organizing_center.index');
    }
}
