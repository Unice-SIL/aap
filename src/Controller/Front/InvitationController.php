<?php


namespace App\Controller\Front;


use App\Entity\Invitation;
use App\Entity\Report;
use App\Utils\Mail\MailHelper;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class InvitationController
 * @package App\Controller\Front
 * @Route("/invitation", name="invitation.")
 */
class InvitationController extends AbstractController
{
    /**
     * @param EntityManagerInterface $entityManager
     * @return Response
     * @Route(name="index", methods={"GET"})
     */
    public function index(EntityManagerInterface $entityManager)
    {
        return $this->render('invitation/index.html.twig', [
            'invitations' => $entityManager->getRepository(Invitation::class)->findByCreatedBy($this->getUser()),
            'layout' => 'top_navbar_layout.html.twig'
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     * @param Request $request
     * @param Invitation $invitation
     * @param TranslatorInterface $translator
     * @return Response
     * @IsGranted(App\Security\InvitationVoter::MANAGE, subject="invitation")
     */
    public function delete(Request $request, Invitation $invitation, TranslatorInterface $translator): Response
    {
        if ($this->isCsrfTokenValid('delete'.$invitation->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($invitation);
            $entityManager->flush();
            $this->addFlash('success', $translator->trans('app.flash_message.delete_success', ['%item%' => $invitation->getName()]));
        }

        return $this->redirect(
            $request->headers->get('referer')
        );
    }

    /**
     * @param Request $request
     * @param Invitation $invitation
     * @param TranslatorInterface $translator
     * @param MailHelper $mailHelper
     * @return Response
     * @throws \Exception
     * @Route("/{id}/resend-invitation", name="resend_invitation", methods={"POST"})
     * @IsGranted(App\Security\InvitationVoter::MANAGE, subject="invitation")
     */
    public function resendInvitation(Request $request, Invitation $invitation, TranslatorInterface $translator, MailHelper $mailHelper): Response
    {
        if (
            $this->isCsrfTokenValid('resend_invitation'.$invitation->getId(), $request->request->get('_token'))
        ) {
            $mailHelper->sendInvitationMail($invitation);

            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', $translator->trans('app.flash_message.invitation_resend_success', ['%invitation%' => $invitation->getName()]));
        }

        return $this->redirect(
            $request->headers->get('referer')
        );
    }
}