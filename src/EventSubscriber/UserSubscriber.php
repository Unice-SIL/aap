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
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function getSubscribedEvents()
    {
        return [
            Events::prePersist,
            Events::preUpdate,
        ];
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        /** @var User $user */
        $user = $args->getObject();

        if (!$user instanceof User) {
            return;
        }

        $this->setPassword($user);

    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        /** @var User $user */
        $user = $args->getObject();

        if (!$user instanceof User) {
            return;
        }

        $this->setPassword($user);

    }

    private function setPassword(User $user)
    {
        $plainPassword = $user->getPlainPassword();
        if ($plainPassword) {
            $encoded = $this->encoder->encodePassword($user, $plainPassword);

            $user->setPassword($encoded);
        }
    }


}
