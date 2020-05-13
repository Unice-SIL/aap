<?php

namespace App\EventSubscriber;

use App\Entity\CallOfProject;
use App\Entity\ProjectFormLayout;
use App\Entity\User;
use App\Manager\ProjectFormLayout\ProjectFormLayoutManagerInterface;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserSubscriber implements EventSubscriber
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    /**
     * CallOfProjectSubscriber constructor.
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     */
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function getSubscribedEvents()
    {
        return [
            Events::prePersist,
        ];
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        /** @var User $user */
        $user = $args->getObject();

        if (!$user instanceof User) {
            return;
        }

        $plainPassword = $user->getPlainPassword();
        $encoded = $this->encoder->encodePassword($user, $plainPassword);

        $user->setPassword($encoded);
    }

}
