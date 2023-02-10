<?php


namespace App\EventSubscriber;


use App\Entity\User;
use App\Manager\User\UserManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;
use Symfony\Contracts\Translation\TranslatorInterface;

class LoginSubscriber implements EventSubscriberInterface
{
    public const COOKIE_NEWS = 'cookie.news';

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
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            SecurityEvents::INTERACTIVE_LOGIN => 'onInteractiveLogin'
        ];
    }

    /**
     * @param InteractiveLoginEvent $event
     * @return void
     */
    public function onInteractiveLogin(InteractiveLoginEvent $event)
    {
        /** @var User $user */
        $user = $event->getAuthenticationToken()->getUser();
        $request = $event->getRequest();
        $this->newsMessage($user);
        $this->welcomeMessage($user);
        $this->setLastConnection($user);
    }

    /**
     * @param User $user
     * @return void
     */
    public function newsMessage(User $user)
    {
        $message = $this->translator->trans('app.message.news');
        $hash = md5($message);
        if ($user->getNews() !== $hash) {
            $user->setNews($hash);
            $this->userManager->update($user);
            $this->flashBag->add('fire', $message);
        }
    }

    /**
     * @param User $user
     * @return void
     */
    public function welcomeMessage(User $user)
    {
        if ($user->getLastConnection() === null) {
            $this->flashBag->add('fire', $this->translator->trans('app.message.first_connection', ['%firstname%' => $user->getFirstname()]));
        }
    }

    /**
     * @param User $user
     * @return void
     */
    public function setLastConnection(User $user)
    {
        $user->setLastConnection(new \DateTime());
        $this->userManager->update($user);
    }

}
