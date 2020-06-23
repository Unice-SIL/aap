<?php


namespace App\EventSubscriber;


use App\Entity\User;
use App\Manager\User\UserManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;
use Symfony\Contracts\Translation\TranslatorInterface;

class LoginSubscriber implements EventSubscriberInterface
{
    /**
     * @var UserManagerInterface
     */
    private $userManager;
    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * LoginSubscriber constructor.
     * @param UserManagerInterface $userManager
     * @param FlashBagInterface $flashBag
     * @param TranslatorInterface $translator
     */
    public function __construct(UserManagerInterface $userManager, FlashBagInterface $flashBag, TranslatorInterface $translator)
    {
        $this->userManager = $userManager;
        $this->flashBag = $flashBag;
        $this->translator = $translator;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            SecurityEvents::INTERACTIVE_LOGIN => 'setLastConnection'
        ];
    }

    /**
     * @param InteractiveLoginEvent $event
     * @throws \Exception
     */
    public function setLastConnection(InteractiveLoginEvent $event)
    {

        /** @var User $user */
        $user = $event->getAuthenticationToken()->getUser();
        if ($user->getLastConnection() === null) {
            $this->flashBag->add('fire', $this->translator->trans('app.message.first_connection', ['%firstname%' => $user->getFirstname()]));
        }
        $user->setLastConnection(new \DateTime());
        $this->userManager->update($user);
    }
}