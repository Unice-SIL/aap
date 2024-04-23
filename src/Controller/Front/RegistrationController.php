<?php

namespace App\Controller\Front;

use App\Entity\Invitation;
use App\Entity\User;
use App\Form\RegistrationForInvitationFormType;
use App\Manager\Invitation\InvitationManagerInterface;
use App\Manager\User\UserManagerInterface;
use App\Security\UserVoter;
use App\Utils\Mail\MailHelper;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register-for-invitation", name="register_for_invitation", methods={"GET", "POST"})
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param UserManagerInterface $userManager
     * @param MailHelper $mailHelper
     * @param InvitationManagerInterface $invitationManager
     * @return Response
     * @throws Exception
     */
    public function registerForInvitation(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        UserManagerInterface $userManager,
        MailHelper $mailHelper,
        InvitationManagerInterface $invitationManager
    ): Response
    {
        $this->denyAccessUnlessGranted(UserVoter::MANAGE_ONE_ORGANIZING_CENTER_OR_CALL_OF_PROJECT_AT_LEAST, $this->getUser());

        $user = $userManager->create();
        $form = $this->createForm(RegistrationForInvitationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    uniqid()
                )
            );
            $user->setUsername(
                $user->getFirstname() .
                ' ' . $user->getLastname() .
                ' (' . $user->getEmail() . ')'
            );

            $invitation = $invitationManager->create($user);
            $invitation->setToken(uniqid());
            $user->setInvitation($invitation);

            $mailHelper->notificationUserInvitation($invitation);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'app.registration.invitation_sent');

            return $this->redirectToRoute('app.register_for_invitation');
        }

        return $this->render('registration/register_for_invitation.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("process-after-shibboleth-connection/{token}", name="process_after_shibboleth_connection")
     * @param Invitation $invitation
     * @param EntityManagerInterface $entityManager
     * @return RedirectResponse
     * @ParamConverter("invitation", options={"mapping": {"token": "token"}})
     */
    public function processAfterShibbolethConnection(Invitation $invitation, EntityManagerInterface $entityManager)
    {

        $guestUser = $invitation->getUser();
        /** @var User $user */
        $user = $this->getUser();

        // we transfer reports from $guestUser To $user
        foreach ($guestUser->getReports() as $report) {

            //tests if user is already reporter on this report Project
            if ($user
                ->getReports()->map(function ($report) {
                    return $report->getProject();
                })->contains($report->getProject())) {
                continue;
            }

            $guestUser->removeReport($report);
            $user->addReport($report);
        }

        // we transfer groups from $guestUser To $user
        foreach ($guestUser->getGroups() as $group) {
            $guestUser->removeGroup($group);
            $user->addGroup($group);
        }


        $invitation->setToken(null);
        $invitation->setUser(null);
        $entityManager->remove($guestUser);
        $invitation->setAcceptedAt(new \DateTime());
        $entityManager->flush();

        return $this->redirectToRoute('app.homepage');
    }
}
