<?php


namespace App\EventSubscriber;


use App\Entity\User;
use App\Manager\User\UserManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

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

    public function __construct(UserManagerInterface $userManager, FlashBagInterface $flashBag)
    {
        $this->userManager = $userManager;
        $this->flashBag = $flashBag;
    }

    public static function getSubscribedEvents()
    {
        return [
            SecurityEvents::INTERACTIVE_LOGIN => 'setLastConnection'
        ];
    }

    public function setLastConnection(InteractiveLoginEvent $event)
    {

        /** @var User $user */
        $user = $event->getAuthenticationToken()->getUser();
        if ($user->getLastConnection() === null) {
            $this->flashBag->add('fire', 'Première connection');
        }
        $user->setLastConnection(new \DateTime());
        $this->userManager->update($user);
    }
}