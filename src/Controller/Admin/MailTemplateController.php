<?php


namespace App\Controller\Admin;

use App\Entity\MailTemplate;
use App\Form\MailTemplate\MailTemplateType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class MailTemplateController
 * @package App\Controller\Admin
 * @Route("/mail_template", name="mail_template.")
 */
class MailTemplateController extends AbstractController
{

    /**
     * @param EntityManagerInterface $em
     * @return Response
     * @Route(name="index", methods={"GET"})
     */
    public function index(EntityManagerInterface $em)
    {
        return $this->render('mail_template/index.html.twig', [
            'mail_templates' => $em->getRepository(MailTemplate::class)->findAll()
        ]);
    }

    /**
     * @param MailTemplate $mailTemplate
     * @param EntityManagerInterface $em
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return RedirectResponse|Response
     * @Route("/{id}/edit", name="edit", methods={"GET", "POST"})
     */
    public function edit(MailTemplate $mailTemplate, EntityManagerInterface $em, Request $request, TranslatorInterface $translator) {

        $form = $this->createForm(MailTemplateType::class, $mailTemplate);
        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {
            $em->flush();
            $this->addFlash('success', $translator->trans('app.flash_message.edit_success', ['%item%' => $mailTemplate->getName()]));

            return $this->redirectToRoute('app.admin.mail_template.index');
        }

        return  $this->render('mail_template/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }
}