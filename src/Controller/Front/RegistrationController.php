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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PropertyAccess\PropertyAccess;
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
     */
    public function registerForInvitation(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        UserManagerInterface $userManager,
        MailHelper $mailHelper,
        InvitationManagerInterface $invitationManager
    ): Response
    {
        $this->denyAccessUnlessGranted(UserVoter::ADMIN_ONE_ORGANIZING_CENTER_OR_CALL_OF_PROJECT_AT_LEAST, $this->getUser());

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

            $mailHelper->sendInvitationMail($invitation);

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
        /** @var User $ldapUser */
        $ldapUser = $this->getUser();
        dump($ldapUser->getReports()->toArray());

        // we transfer the $propertiesOfElementToTransfer properties from the $guestUser to the $ldapUser:
        $propertiesOfElementToTransfer = ['reports', 'groups'];
        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        foreach ($propertiesOfElementToTransfer as $property) {
            $data = $propertyAccessor->getValue($guestUser, $property);
            $propertyAccessor->setValue($ldapUser, $property, $data);
        }

        $entityManager->remove($guestUser);
        dump($ldapUser->getReports()->toArray());
        dd('ok');
        $invitation->setToken(null);
        $entityManager->flush();

        return $this->redirectToRoute('app.homepage');
    }
}
