<?php


namespace App\EventSubscriber;



use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserTypeSubscriber implements EventSubscriberInterface
{

    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    /**
     * CallOfProjectSubscriber constructor.
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public static function getSubscribedEvents()
    {
        return [
                FormEvents::POST_SUBMIT => 'postSubmit',
        ];
    }


    public function postSubmit(FormEvent $event)
    {
        /** @var User $user */
        $user = $event->getData();

        $plainPassword = $user->getPlainPassword();

        if ($plainPassword) {
            $encoded = $this->encoder->encodePassword($user, $plainPassword);

            $user->setPassword($encoded);
        }
    }
}